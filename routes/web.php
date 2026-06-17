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
use App\Http\Controllers\AccountabilityPartnerController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\LeaderboardController;
use App\Models\ChatLog;
use App\Models\Course;
use App\Models\LessonCompletion;
use App\Http\Controllers\ChatController;

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
    // Validate the incoming request data including gender
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'gender' => 'required|string|in:male,female'
    ]);

    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('profiles', 'public');
    }

    // Create the user with the selected gender
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'user',
        'is_admin' => 0,
        'image' => $imagePath,
        'gender' => $request->gender,
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
| User Dashboard & Profile Routes
|--------------------------------------------------------------------------
*/

Route::get('/profile', function () {
    return view('profile');
})->name('profile')->middleware('auth');

Route::get('/my-dashboard', function () {
    $user = Auth::user();

    // Get total points directly from authenticated user
    $points = $user->total_points ?? 0;

    // Use the dynamic accessor level we created in the User model
    $badge = $user->level;

    $chartLabels = [];
    $chartData = [];

    // Loop through the last 7 days to calculate accurate daily scores
    for ($i = 6; $i >= 0; $i--) {
        $date = Carbon::now()->subDays($i)->format('Y-m-d');
        $chartLabels[] = Carbon::parse($date)->format('M d');

        $tracker = IbadahTracker::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->first();

        $dailyScore = 0;

        if ($tracker) {
            // 1. Calculate points for all 5 Farz prayers
            $prayers = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];
            foreach ($prayers as $prayer) {
                if ($tracker->$prayer === 'jamaah_mosque')
                    $dailyScore += 10;
                elseif ($tracker->$prayer === 'jamaah_home')
                    $dailyScore += 7;
                elseif ($tracker->$prayer === 'alone')
                    $dailyScore += 5;
                elseif ($tracker->$prayer === 'qada')
                    $dailyScore += 2;
            }

            // 2. Calculate points for Sunnah, Adhkar, and Good Deeds (5 points each)
            $deeds = ['morning_adhkar', 'evening_adhkar', 'tahajjud', 'witr', 'sadaqah', 'duwa'];
            foreach ($deeds as $deed) {
                if ($tracker->$deed == 1)
                    $dailyScore += 5;
            }

            // 3. Calculate points for Quran Recitation (2 points per page)
            if ($tracker->quran_pages > 0)
                $dailyScore += ($tracker->quran_pages * 2);

            // 4. Add points based on Khushu Level
            if ($tracker->khushu_level > 0)
                $dailyScore += $tracker->khushu_level;
        }

        $chartData[] = $dailyScore;
    }

    // Strictly passing only chart and user details to keep it clean
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
| Community Feed, Likes & Comments Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
    Route::post('/feed', [FeedController::class, 'store'])->name('feed.store');

    Route::get('/messages/{partner?}', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/messages/{partner}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    // Dynamic interaction routes for posts
    Route::post('/feed/posts/{post}/like', [FeedController::class, 'toggleLike'])->name('posts.like');
    Route::post('/feed/posts/{post}/comments', [FeedController::class, 'storeComment'])->name('comments.store');
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

    // Comment Likes & Replies
    Route::post('/comment/{comment}/like', [FeedController::class, 'toggleCommentLike'])->name('comments.like');
    Route::post('/comment/{comment}/reply', [FeedController::class, 'storeReply'])->name('comments.reply');
});

// Update your existing /tracker route to include the Daily Spiritual Lesson
Route::get('/tracker', function () {
    $lessons = [
        "\"Verily, in the remembrance of Allah do hearts find rest.\" (Ar-Rad: 28) - Make today count by keeping your tongue moist with Adhkar.",
        "The Prophet (ﷺ) said: 'The closest a servant comes to his Lord is when he is in prostration (Sujood).' Enhance your Khushu today.",
        "Anas ibn Malik reported: The Prophet (ﷺ) was the most generous of people. Don't forget to give a small Sadaqah today, even a smile!",
        "\"Establish prayer, for indeed, prayer prohibits immorality and wrongdoing.\" (Al-Ankabut: 45) - Aim for all 5 prayers in the Mosque today.",
        "The best among you are those who learn the Quran and teach it. Try to reflect deeply on at least one verse today."
    ];
    // Pick a deterministic lesson based on the day of the month
    $spiritualLesson = $lessons[date('j') % count($lessons)];

    return view('tracker', compact('spiritualLesson'));
});

/*
|--------------------------------------------------------------------------
| Leaderboard & Accountability Partner Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/community', [AccountabilityPartnerController::class, 'index'])->name('community.index');
    Route::post('/partner/request/{id}', [AccountabilityPartnerController::class, 'sendRequest'])->name('partner.request');
    Route::post('/partner/accept/{id}', [AccountabilityPartnerController::class, 'acceptRequest'])->name('partner.accept');
    Route::post('/partner/reject/{id}', [AccountabilityPartnerController::class, 'rejectRequest'])->name('partner.reject');
});

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
    $userMessage = $request->input('message');

    try {
        $response = Http::timeout(60)->post('http://127.0.0.1:5000/chat', [
            'message' => $userMessage
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $aiReply = $data['reply'] ?? $data['response'] ?? 'I could not process the response properly.';

            return response()->json([
                'success' => true,
                'reply' => $aiReply
            ]);
        } else {
            return response()->json([
                'success' => false,
                'reply' => 'Sorry, Noor AI server returned an error: ' . $response->status()
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'reply' => 'Failed to connect to Noor AI. Please ensure your Python AI Server is running on port 5000. Error: ' . $e->getMessage()
        ]);
    }
})->name('web.chat')->middleware('auth');

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