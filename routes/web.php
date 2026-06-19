<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// --- Models ---
use App\Models\User;
use App\Models\IbadahTracker;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonCompletion;

// --- User Controllers ---
use App\Http\Controllers\Api\IbadahTrackerController;
use App\Http\Controllers\AccountabilityPartnerController;
use App\Http\Controllers\PostController; // Handles Cloudinary post uploads
use App\Http\Controllers\FeedController; // Handles likes & comments
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\QuizController;

// --- Admin Controllers ---
use App\Http\Controllers\Admin\SeerahController;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Admin\HadithController;

/*
|--------------------------------------------------------------------------
| Public & Authentication Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// Show Login Form
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Handle Login Request
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/my-dashboard');
    }

    return back()->withErrors(['email' => 'Invalid credentials']);
});

// Show Registration Form
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Handle Registration Request
Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        'gender' => 'required|string|in:male,female'
    ]);

    $imagePath = null;

    if ($request->hasFile('image')) {
        $imagePath = app(\App\Services\CloudinaryService::class)->uploadImage($request->file('image'));

        // Assign uploaded image path to variable; will be set on user creation below
        // (Do not assign to $user before it is created)
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

// Handle Logout
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

    // --- Profile & Dashboard ---
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    Route::get('/my-dashboard', function () {
        $user = Auth::user();
        $points = $user->total_points ?? 0;
        $badge = $user->level;

        $chartLabels = [];
        $chartData = [];

        // Generate tracking data for the last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = Carbon::parse($date)->format('M d');

            $tracker = IbadahTracker::where('user_id', $user->id)
                ->whereDate('date', $date)
                ->first();

            $dailyScore = 0;

            if ($tracker) {
                // Calculate prayer points
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

                // Calculate extra deeds points
                $deeds = ['morning_adhkar', 'evening_adhkar', 'tahajjud', 'witr', 'sadaqah', 'duwa'];
                foreach ($deeds as $deed) {
                    if ($tracker->$deed == 1)
                        $dailyScore += 5;
                }

                // Quran and Khushu points
                if ($tracker->quran_pages > 0)
                    $dailyScore += ($tracker->quran_pages * 2);
                if ($tracker->khushu_level > 0)
                    $dailyScore += $tracker->khushu_level;
            }

            $chartData[] = $dailyScore;
        }

        return view('dashboard', compact('user', 'points', 'badge', 'chartLabels', 'chartData'));
    });

    // --- Community Feed & Social Posts ---
    // Updated to use PostController for the main feed and post creation (handles Cloudinary)
    Route::get('/feed', [PostController::class, 'index'])->name('feed.index');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{id}', [App\Http\Controllers\FeedController::class, 'destroy'])->name('posts.destroy');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{id}/report', [App\Http\Controllers\FeedController::class, 'report'])->name('posts.report');

    // Likes & Comments handled by FeedController
    Route::post('/feed/posts/{post}/like', [FeedController::class, 'toggleLike'])->name('posts.like');
    Route::post('/feed/posts/{post}/comments', [FeedController::class, 'storeComment'])->name('comments.store');
    Route::post('/comment/{comment}/like', [FeedController::class, 'toggleCommentLike'])->name('comments.like');
    Route::post('/comment/{comment}/reply', [FeedController::class, 'storeReply'])->name('comments.reply');

    // --- Global Leaderboard & Partner Chat ---
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
    Route::get('/messages/{partner?}', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/messages/{partner}/send', [ChatController::class, 'sendMessage'])->name('chat.send');

    // --- Accountability Partner Management ---
    Route::get('/community', [AccountabilityPartnerController::class, 'index'])->name('community.index');
    Route::post('/partner/request/{id}', [AccountabilityPartnerController::class, 'sendRequest'])->name('partner.request');
    Route::post('/partner/accept/{id}', [AccountabilityPartnerController::class, 'acceptRequest'])->name('partner.accept');
    Route::post('/partner/reject/{id}', [AccountabilityPartnerController::class, 'rejectRequest'])->name('partner.reject');

    // --- Ibadah Tracker ---
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

    // --- Learning Management System (LMS) User Routes ---
    Route::get('/courses', function () {
        $courses = Course::latest()->get();
        return view('lms.index', compact('courses'));
    })->name('courses.catalog');

    Route::get('/lms', function () {
        $courses = Course::all();
        return view('lms', compact('courses'));
    })->name('lms.index');

    Route::get('/lms/{id}', function ($id) {
        $course = Course::findOrFail($id);


        $quizzes = \App\Models\Quiz::latest()->get();

        $completedLessonIds = LessonCompletion::where('user_id', auth()->id())->pluck('lesson_id')->toArray();

        return view('lms-details', compact('course', 'completedLessonIds', 'quizzes'));
    })->name('lms.show');

    Route::get('/lesson/{id}', function ($id) {
        $lesson = Lesson::findOrFail($id);
        return view('lesson-view', compact('lesson'));
    })->name('lesson.view');

    Route::post('/lesson/{id}/complete', function ($id) {
        LessonCompletion::firstOrCreate(['user_id' => Auth::id(), 'lesson_id' => $id]);
        return back()->with('success', 'Lesson completed successfully!');
    })->name('lesson.complete');

    // --- User Quizzes ---
    // Student Quiz Routes
    Route::get('/quizzes/{id}', [\App\Http\Controllers\StudentQuizController::class, 'show'])->name('student.quizzes.show');
    Route::post('/quizzes/{id}/submit', [\App\Http\Controllers\StudentQuizController::class, 'submit'])->name('student.quizzes.submit');
    Route::get('/admin/quizzes/{id}/edit', [\App\Http\Controllers\Admin\QuizController::class, 'edit'])->name('admin.quizzes.edit');
    Route::put('/admin/quizzes/{id}', [\App\Http\Controllers\Admin\QuizController::class, 'update'])->name('admin.quizzes.update');
    Route::get('/quiz/{id}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quiz/{id}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');

    // --- Noor AI Chatbot Integration ---
    Route::get('/noor-ai', function () {
        return view('noor-ai');
    })->name('noor.index');

    Route::post('/web-chat', function (Request $request) {
        $userMessage = $request->input('message');
        try {
            // Forwarding the request to the Python Flask server
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

    // Admin Quiz Management
    Route::get('/quizzes', [AdminQuizController::class, 'index'])->name('quizzes.index');
    Route::delete('/quizzes/{quiz}', [AdminQuizController::class, 'destroy'])->name('quizzes.destroy');
    Route::get('/quizzes/create', [AdminQuizController::class, 'create'])->name('quizzes.create');
    Route::post('/quizzes/store', [AdminQuizController::class, 'store'])->name('quizzes.store');

    // Admin Seerah Uploads
    Route::get('/seerah', [SeerahController::class, 'index'])->name('seerah.index');
    Route::get('/seerah/upload', [SeerahController::class, 'create'])->name('seerah.create');
    Route::post('/seerah/upload', [SeerahController::class, 'store'])->name('seerah.store');

    // Admin Course Management
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

    // Admin Module Management
    Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');
    Route::get('/modules/create', [ModuleController::class, 'create'])->name('modules.create');
    Route::post('/modules', [ModuleController::class, 'store'])->name('modules.store');
    Route::get('/modules/{module}/edit', [ModuleController::class, 'edit'])->name('modules.edit');
    Route::put('/modules/{module}', [ModuleController::class, 'update'])->name('modules.update');
    Route::delete('/modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');

    // Admin Lesson Management
    Route::get('/lessons', [AdminLessonController::class, 'index'])->name('lessons.index');
    Route::get('/lessons/create', [AdminLessonController::class, 'create'])->name('lessons.create');
    Route::post('/lessons', [AdminLessonController::class, 'store'])->name('lessons.store');
    Route::get('/lessons/{lesson}/edit', [AdminLessonController::class, 'edit'])->name('lessons.edit');
    Route::put('/lessons/{lesson}', [AdminLessonController::class, 'update'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [AdminLessonController::class, 'destroy'])->name('lessons.destroy');
});

// Database Connection Checker
Route::get('/check-db', function () {
    try {
        $dbName = DB::connection()->getDatabaseName();
        return "Laravel is currently connected to the database: " . $dbName;
    } catch (\Exception $e) {
        return "Connection Error: " . $e->getMessage();
    }
});

// Admin Hadith Routes
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/hadiths/{id}/edit', [App\Http\Controllers\Admin\HadithController::class, 'edit'])->name('admin.hadiths.edit');
    Route::put('/hadiths/{id}', [App\Http\Controllers\Admin\HadithController::class, 'update'])->name('admin.hadiths.update');

    Route::get('/hadiths', [App\Http\Controllers\Admin\HadithController::class, 'index'])->name('admin.hadiths.index');
    Route::get('/hadiths/create', [App\Http\Controllers\Admin\HadithController::class, 'create'])->name('admin.hadiths.create');
    Route::post('/hadiths', [App\Http\Controllers\Admin\HadithController::class, 'store'])->name('admin.hadiths.store');
});

// Student/User Hadith Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/hadiths/chapter/{id}', [App\Http\Controllers\HadithController::class, 'chapter'])->name('hadiths.chapter');
    Route::get('/hadiths', [App\Http\Controllers\HadithController::class, 'index'])->name('hadiths.index');
    Route::get('/hadiths/category/{slug}', [App\Http\Controllers\HadithController::class, 'category'])->name('hadiths.category');
    Route::post('/hadiths/{id}/read', [App\Http\Controllers\HadithController::class, 'markAsRead'])->name('hadiths.read');
});

