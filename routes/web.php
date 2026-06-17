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
use App\Http\Controllers\ChatController;
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
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'gender' => 'required|string|in:male,female'
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
| Authenticated User Routes (Protected by Auth Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // User Dashboard & Profile
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

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
    })->name('profile.update');

    Route::get('/my-dashboard', function () {
        $user = Auth::user();
        $points = $user->total_points ?? 0;
        $badge = $user->level;

        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = Carbon::parse($date)->format('M d');

            $tracker = IbadahTracker::where('user_id', $user->id)
                ->whereDate('date', $date)
                ->first();

            $dailyScore = 0;

            if ($tracker) {
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

                $deeds = ['morning_adhkar', 'evening_adhkar', 'tahajjud', 'witr', 'sadaqah', 'duwa'];
                foreach ($deeds as $deed) {
                    if ($tracker->$deed == 1)
                        $dailyScore += 5;
                }

                if ($tracker->quran_pages > 0)
                    $dailyScore += ($tracker->quran_pages * 2);
                if ($tracker->khushu_level > 0)
                    $dailyScore += $tracker->khushu_level;
            }

            $chartData[] = $dailyScore;
        }

        return view('dashboard', compact('user', 'points', 'badge', 'chartLabels', 'chartData'));
    });

    // Community Feed, Likes & Comments
    Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
    Route::post('/feed', [FeedController::class, 'store'])->name('feed.store');
    Route::post('/feed/posts/{post}/like', [FeedController::class, 'toggleLike'])->name('posts.like');
    Route::post('/feed/posts/{post}/comments', [FeedController::class, 'storeComment'])->name('comments.store');

    // Comment Support Likes & Nested Replies
    Route::post('/comment/{comment}/like', [FeedController::class, 'toggleCommentLike'])->name('comments.like');
    Route::post('/comment/{comment}/reply', [FeedController::class, 'storeReply'])->name('comments.reply');

    // Global Leaderboard & Partner Chat
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
    Route::get('/messages/{partner?}', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/messages/{partner}/send', [ChatController::class, 'sendMessage'])->name('chat.send');

    // Accountability Partner Management
    Route::get('/community', [AccountabilityPartnerController::class, 'index'])->name('community.index');
    Route::post('/partner/request/{id}', [AccountabilityPartnerController::class, 'sendRequest'])->name('partner.request');
    Route::post('/partner/accept/{id}', [AccountabilityPartnerController::class, 'acceptRequest'])->name('partner.accept');
    Route::post('/partner/reject/{id}', [AccountabilityPartnerController::class, 'rejectRequest'])->name('partner.reject');

    // Fixed & Unified Ibadah Tracker Route with Spiritual Lessons
    Route::get('/tracker', function () {
        $lessons = [
            "\"Verily, in the remembrance of Allah do hearts find rest.\" (Ar-Rad: 28) - Make today count by keeping your tongue moist with Adhkar.",
            "The Prophet (ﷺ) said: 'The closest a servant comes to his Lord is when he is in prostration (Sujood).' Enhance your Khushu today.",
            "Anas ibn Malik reported: The Prophet (ﷺ) was the most generous of people. Don't forget to give a small Sadaqah today, even a smile!",
            "\"Establish prayer, for indeed, prayer prohibits immorality and wrongdoing.\" (Al-Ankabut: 45) - Aim for all 5 prayers in the Mosque today.",
            "The best among you are those who learn the Quran and teach it. Try to reflect deeply on at least one verse today."
        ];
        $spiritualLesson = $lessons[date('j') % count($lessons)];

        return view('tracker', compact('spiritualLesson'));
    })->name('tracker.index');

    Route::post('/tracker', [IbadahTrackerController::class, 'store']);

    // Learning Management System (LMS) User Routes
    Route::get('/courses', function () {
        $courses = Course::latest()->get();
        return view('courses', compact('courses'));
    })->name('courses.catalog');

    Route::get('/lms', function () {
        $courses = Course::all();
        return view('lms', compact('courses'));
    })->name('lms.index');

    Route::get('/lms/{id}', function ($id) {
        $course = Course::findOrFail($id);
        $completedLessonIds = LessonCompletion::where('user_id', auth()->id())->pluck('lesson_id')->toArray();
        return view('lms-details', compact('course', 'completedLessonIds'));
    })->name('lms.show');

    Route::get('/lesson/{id}', function ($id) {
        $lesson = \App\Models\Lesson::findOrFail($id);
        return view('lesson-view', compact('lesson'));
    })->name('lesson.view');

    Route::post('/lesson/{id}/complete', function ($id) {
        LessonCompletion::firstOrCreate(['user_id' => Auth::id(), 'lesson_id' => $id]);
        return back()->with('success', 'Lesson completed successfully!');
    })->name('lesson.complete');

    // Noor AI Chatbot Core Integration
    Route::get('/noor-ai', function () {
        return view('noor-ai');
    })->name('noor.index');

    Route::post('/web-chat', function (Request $request) {
        $userMessage = $request->input('message');
        try {
            $response = Http::timeout(60)->post('http://127.0.0.1:5000/chat', [
                'message' => $userMessage
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $aiReply = $data['reply'] ?? $data['response'] ?? 'I could not process the response properly.';
                return response()->json(['success' => true, 'reply' => $aiReply]);
            }
            return response()->json(['success' => false, 'reply' => 'Sorry, Noor AI server returned an error: ' . $response->status()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'reply' => 'Failed to connect to Noor AI. Error: ' . $e->getMessage()]);
        }
    })->name('web.chat');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes (Protected by Auth & Admin Middleware)
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