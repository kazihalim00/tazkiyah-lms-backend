<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\IbadahTracker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Api\IbadahTrackerController;
use App\Models\ChatLog;
use App\Models\Course;
use App\Models\LessonCompletion;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/my-dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
});

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
    ]);

    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('profiles', 'public');
    }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'user',
        'is_admin' => 0,
        'image' => $imagePath,
        'total_points' => 0,
    ]);

    Auth::login($user);
    return redirect('/my-dashboard');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| User Dashboard & Tracking Routes
|--------------------------------------------------------------------------
*/
Route::get('/profile', function () {
    return view('profile');
})->name('profile')->middleware('auth');

Route::get('/my-dashboard', function () {
    $user = Auth::user();
    $points = $user->total_points;
    $badge = 'Seeker';

    if ($points >= 50)
        $badge = 'Fajr Warrior';
    if ($points >= 150)
        $badge = 'Consistent Believer';
    if ($points >= 300)
        $badge = 'Tazkiyah Master';

    $chartLabels = [];
    $chartData = [];

    for ($i = 6; $i >= 0; $i--) {
        $date = Carbon::now()->subDays($i)->format('Y-m-d');
        $chartLabels[] = Carbon::parse($date)->format('M d');
        $tracker = IbadahTracker::where('user_id', $user->id)->where('date', $date)->first();
        $dailyScore = 0;

        if ($tracker) {
            if ($tracker->fajr === 'Jamaah')
                $dailyScore += 10;
            elseif ($tracker->fajr === 'Alone')
                $dailyScore += 5;
            if ($tracker->morning_adhkar)
                $dailyScore += 5;
        }
        $chartData[] = $dailyScore;
    }

    return view('dashboard', compact('user', 'points', 'badge', 'chartLabels', 'chartData'));
})->middleware('auth');

Route::post('/profile/update', function (Request $request) {
    $user = Auth::user();

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
    ]);

    $user->name = $request->name;
    $user->email = $request->email;

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('profiles', 'public');
        $user->image = $imagePath;
    }

    $user->save();

    return back()->with('success', 'Profile updated successfully!');
})->name('profile.update')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Course Catalog Routes
|--------------------------------------------------------------------------
*/

Route::get('/courses', function () {
    $courses = \App\Models\Course::latest()->get();
    return view('courses', compact('courses'));
})->name('courses.catalog')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Noor AI Chatbot Routes
|--------------------------------------------------------------------------
*/

Route::get('/noor-ai', function () {
    return view('noor-ai');
})->middleware('auth');

Route::post('/web-chat', function (Request $request) {
    $request->validate(['message' => 'required|string']);
    $user = Auth::user();
    $userMessage = $request->message;

    $chatLog = ChatLog::create([
        'user_id' => $user->id,
        'user_message' => $userMessage,
    ]);

    try {
        $response = Http::post('http://127.0.0.1:5000/api/chat', ['message' => $userMessage]);

        if ($response->successful()) {
            $aiData = $response->json();
            $chatLog->update([
                'ai_response' => $aiData['response'] ?? 'I am here to listen.',
                'mood_tag' => $aiData['mood'] ?? null,
            ]);
        } else {
            $chatLog->update(['ai_response' => 'Sorry, Noor AI is currently taking a break.']);
        }
    } catch (\Exception $e) {
        $chatLog->update(['ai_response' => 'Connection to Noor AI failed.']);
    }

    return response()->json(['success' => true, 'reply' => $chatLog->ai_response]);
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| Ibadah Tracker & LMS Routes
|--------------------------------------------------------------------------
*/

Route::get('/tracker', function () {
    return view('tracker');
})->middleware('auth');

Route::post('/tracker', [IbadahTrackerController::class, 'store'])->middleware('auth');

Route::get('/lms', function () {
    $courses = Course::all();
    return view('lms', compact('courses'));
})->middleware('auth');

Route::get('/lms/{id}', function ($id) {
    $course = \App\Models\Course::findOrFail($id);
    $completedLessonIds = \App\Models\LessonCompletion::where('user_id', auth()->id())->pluck('lesson_id')->toArray();
    return view('lms-details', compact('course', 'completedLessonIds'));
})->middleware('auth');

Route::get('/lesson/{id}', function ($id) {
    $lesson = \App\Models\Lesson::findOrFail($id);
    return view('lesson-view', compact('lesson'));
})->middleware('auth');

Route::post('/lesson/{id}/complete', function ($id) {
    LessonCompletion::firstOrCreate(['user_id' => Auth::id(), 'lesson_id' => $id]);
    return back()->with('success', 'Lesson completed successfully!');
})->name('lesson.complete')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Course management
    Route::get('/courses', [\App\Http\Controllers\Admin\CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [\App\Http\Controllers\Admin\CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [\App\Http\Controllers\Admin\CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [\App\Http\Controllers\Admin\CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'destroy'])->name('courses.destroy');

    // Module management
    Route::get('/modules', [\App\Http\Controllers\Admin\ModuleController::class, 'index'])->name('modules.index');
    Route::get('/modules/create', [\App\Http\Controllers\Admin\ModuleController::class, 'create'])->name('modules.create');
    Route::post('/modules', [\App\Http\Controllers\Admin\ModuleController::class, 'store'])->name('modules.store');
    Route::get('/modules/{module}/edit', [\App\Http\Controllers\Admin\ModuleController::class, 'edit'])->name('modules.edit');
    Route::put('/modules/{module}', [\App\Http\Controllers\Admin\ModuleController::class, 'update'])->name('modules.update');
    Route::delete('/modules/{module}', [\App\Http\Controllers\Admin\ModuleController::class, 'destroy'])->name('modules.destroy');

    // Lesson management
    Route::get('/lessons', [\App\Http\Controllers\Admin\LessonController::class, 'index'])->name('lessons.index');
    Route::get('/lessons/create', [\App\Http\Controllers\Admin\LessonController::class, 'create'])->name('lessons.create');
    Route::post('/lessons', [\App\Http\Controllers\Admin\LessonController::class, 'store'])->name('lessons.store');
    Route::get('/lessons/{lesson}/edit', [\App\Http\Controllers\Admin\LessonController::class, 'edit'])->name('lessons.edit');
    Route::put('/lessons/{lesson}', [\App\Http\Controllers\Admin\LessonController::class, 'update'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [\App\Http\Controllers\Admin\LessonController::class, 'destroy'])->name('lessons.destroy');
});