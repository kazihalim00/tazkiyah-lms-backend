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

// Redirect root URL to the login page
Route::get('/', function () {
    return redirect('/login');
});

// Show the Login Page
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Process the Login Request
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    // Attempt to authenticate the user
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/my-dashboard');
    }

    // Return back with an error if authentication fails
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
});

// Show the Registration Page
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Process the Registration Request
Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed'
    ]);

    // Create a new user record in the database
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'user',
        'total_points' => 0,
    ]);

    // Automatically log the user in after registration
    Auth::login($user);
    return redirect('/my-dashboard');
});

// Process the Logout Request
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

// Secured Dashboard Route (Accessible only to authenticated users)
Route::get('/my-dashboard', function () {
    // Retrieve the currently authenticated user
    $user = Auth::user();

    // Determine the user's badge based on total points
    $points = $user->total_points;
    $badge = 'Seeker';

    if ($points >= 50)
        $badge = 'Fajr Warrior';
    if ($points >= 150)
        $badge = 'Consistent Believer';
    if ($points >= 300)
        $badge = 'Tazkiyah Master';

    // Prepare data for the 7-day activity chart
    $chartLabels = [];
    $chartData = [];

    for ($i = 6; $i >= 0; $i--) {
        $date = Carbon::now()->subDays($i)->format('Y-m-d');
        $chartLabels[] = Carbon::parse($date)->format('M d');

        // Fetch daily tracker data to calculate the daily score
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

/*
|--------------------------------------------------------------------------
| Noor AI Chatbot Routes
|--------------------------------------------------------------------------
*/

// Show the Noor AI Chat Page
Route::get('/noor-ai', function () {
    return view('noor-ai');
})->middleware('auth');

// Process Noor AI Web Chat requests
Route::post('/web-chat', function (Request $request) {
    $request->validate(['message' => 'required|string']);
    $user = Auth::user();
    $userMessage = $request->message;

    // Save the user's message to the chat log
    $chatLog = ChatLog::create([
        'user_id' => $user->id,
        'user_message' => $userMessage,
    ]);

    try {
        // Forward the message to the Python Flask server API
        $response = Http::post('http://127.0.0.1:5000/api/chat', [
            'message' => $userMessage
        ]);

        // Update the chat log with the AI's response
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
        // Handle server connection errors
        $chatLog->update(['ai_response' => 'Connection to Noor AI failed. Please ensure the Python server is running.']);
    }

    return response()->json([
        'success' => true,
        'reply' => $chatLog->ai_response
    ]);
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| Ibadah Tracker Routes
|--------------------------------------------------------------------------
*/

// Show the Daily Ibadah Tracker Page
Route::get('/tracker', function () {
    return view('tracker');
})->middleware('auth');

// Save Daily Ibadah Tracker Data
Route::post('/tracker', [IbadahTrackerController::class, 'store'])->middleware('auth');

/*
|--------------------------------------------------------------------------
| Learning Management System (LMS) Routes
|--------------------------------------------------------------------------
*/

// Show the list of all available courses
Route::get('/lms', function () {
    $courses = Course::all();
    return view('lms', compact('courses'));
})->middleware('auth');

// Show specific course details and track progress
Route::get('/lms/{id}', function ($id) {
    $course = \App\Models\Course::findOrFail($id);

    $completedLessonIds = \App\Models\LessonCompletion::where('user_id', auth()->id())
        ->pluck('lesson_id')
        ->toArray();

    return view('lms-details', compact('course', 'completedLessonIds'));
})->middleware('auth');

// View a specific lesson's content
Route::get('/lesson/{id}', function ($id) {
    $lesson = \App\Models\Lesson::findOrFail($id);
    return view('lesson-view', compact('lesson'));
})->middleware('auth');


// Mark a specific lesson as completed
Route::post('/lesson/{id}/complete', function ($id) {
    $user = Auth::user();

    LessonCompletion::firstOrCreate([
        'user_id' => $user->id,
        'lesson_id' => $id
    ]);

    return back()->with('success', 'Lesson completed successfully! Great job!');
})->name('lesson.complete')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/

// Grouping all admin routes under the '/admin' prefix
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    // Display the list of all lessons in the admin panel
    Route::get('/lessons', [\App\Http\Controllers\Admin\LessonController::class, 'index'])->name('lessons.index');

    // Show the form to create a new lesson
    Route::get('/lessons/create', [\App\Http\Controllers\Admin\LessonController::class, 'create'])->name('lessons.create');

    // Store the newly created lesson in the database
    Route::post('/lessons', [\App\Http\Controllers\Admin\LessonController::class, 'store'])->name('lessons.store');

    // Delete a specific lesson from the database
    Route::delete('/lessons/{lesson}', [\App\Http\Controllers\Admin\LessonController::class, 'destroy'])->name('lessons.destroy');

    // Uodate a specific lesson from the database
    Route::get('/lessons/{lesson}/edit', [\App\Http\Controllers\Admin\LessonController::class, 'edit'])->name('lessons.edit');

    // Update a specific lesson from the database
    Route::put('/lessons/{lesson}', [\App\Http\Controllers\Admin\LessonController::class, 'update'])->name('lessons.update');

    // Courses route
    Route::get('/courses', [\App\Http\Controllers\Admin\CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [\App\Http\Controllers\Admin\CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [\App\Http\Controllers\Admin\CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [\App\Http\Controllers\Admin\CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'destroy'])->name('courses.destroy');
    // Module route
    Route::get('/modules', [\App\Http\Controllers\Admin\ModuleController::class, 'index'])->name('modules.index');
    Route::get('/modules/create', [\App\Http\Controllers\Admin\ModuleController::class, 'create'])->name('modules.create');
    Route::post('/modules', [\App\Http\Controllers\Admin\ModuleController::class, 'store'])->name('modules.store');
    Route::get('/modules/{module}/edit', [\App\Http\Controllers\Admin\ModuleController::class, 'edit'])->name('modules.edit');
    Route::put('/modules/{module}', [\App\Http\Controllers\Admin\ModuleController::class, 'update'])->name('modules.update');
    Route::delete('/modules/{module}', [\App\Http\Controllers\Admin\ModuleController::class, 'destroy'])->name('modules.destroy');
});