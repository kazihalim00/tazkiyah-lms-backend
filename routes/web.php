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
use App\Http\Controllers\PostController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuranController;
use App\Http\Controllers\HadithController;
use App\Http\Controllers\StudentQuizController;

// --- Admin Controllers ---
use App\Http\Controllers\Admin\SeerahController;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Admin\HadithController as AdminHadithController;
use App\Http\Controllers\DonationController;

/*
|--------------------------------------------------------------------------
| Public & Authentication Routes
|--------------------------------------------------------------------------
*/

// Home landing page route
Route::get('/', function () {
    $topUsers = collect([
        (object) ['name' => 'Ishtiaque Ahmed Sojib', 'points' => 695, 'initials' => 'IA'],
        (object) ['name' => 'Rafid Hasan Sydney', 'points' => 651, 'initials' => 'RH'],
        (object) ['name' => 'Abraham John', 'points' => 193, 'initials' => 'AJ'],
    ]);

    $courses = collect([
        (object) [
            'title' => 'Seerah of Prophet Muhammad (ﷺ)',
            'progress' => 45,
            'video_url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
            'thumbnail' => 'https://images.unsplash.com/photo-1604871000636-074FA5117945?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            'status' => 'FREE',
            'category' => 'Islamic History'
        ]
    ]);

    return view('welcome', compact('topUsers', 'courses'));
})->name('home');

// Login view route
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Login form submission handler
Route::post('/login', function (Request $request) {
    $credentials = $request->validate(['email' => 'required|email', 'password' => 'required']);
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/my-dashboard');
    }
    return back()->withErrors(['email' => 'Invalid credentials']);
});

// Register view route
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Register form submission handler
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

// Logout handler
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Donation routes
Route::get('/donate', [DonationController::class, 'index'])->name('donate.index');
Route::post('/donate/pay', [DonationController::class, 'pay'])->name('donate.pay');

/*
|--------------------------------------------------------------------------
| Authenticated & Verified User Routes (Protected by Auth & Verified Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Profile Routes
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');
    Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Dashboard Route (Requires verified email)
    Route::get('/my-dashboard', function () {
        $user = Auth::user();
        $points = $user->total_points ?? 0;
        $badge = $user->level;
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = Carbon::parse($date)->format('M d');
            $tracker = IbadahTracker::where('user_id', $user->id)->whereDate('date', $date)->first();
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
                if ($tracker->bonus_points > 0)
                    $dailyScore += $tracker->bonus_points;
            }
            $chartData[] = $dailyScore;
        }

        return view('dashboard', compact('user', 'points', 'badge', 'chartLabels', 'chartData'));
    });

    // Feed & Community Routes
    Route::get('/feed', [PostController::class, 'index'])->name('feed.index');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{id}', [FeedController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{id}/report', [FeedController::class, 'report'])->name('posts.report');
    Route::post('/feed/posts/{post}/like', [FeedController::class, 'toggleLike'])->name('posts.like');
    Route::post('/feed/posts/{post}/comments', [FeedController::class, 'storeComment'])->name('comments.store');
    Route::post('/comment/{comment}/like', [FeedController::class, 'toggleCommentLike'])->name('comments.like');
    Route::post('/comment/{comment}/reply', [FeedController::class, 'storeReply'])->name('comments.reply');

    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
    Route::get('/messages/{partner?}', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/messages/{partner}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/messages/{message}/react', [ChatController::class, 'react'])->name('chat.react');

    Route::get('/community', [AccountabilityPartnerController::class, 'index'])->name('community.index');
    Route::post('/partner/request/{id}', [AccountabilityPartnerController::class, 'sendRequest'])->name('partner.request');
    Route::post('/partner/accept/{id}', [AccountabilityPartnerController::class, 'acceptRequest'])->name('partner.accept');
    Route::post('/partner/reject/{id}', [AccountabilityPartnerController::class, 'rejectRequest'])->name('partner.reject');

    // Tracker Routes
    Route::get('/tracker', function () {
        $lessons = [
            "\"Verily, in the remembrance of Allah do hearts find rest.\" (Ar-Rad: 28) - Make today count.",
            "The Prophet (ﷺ) said: 'The closest a servant comes to his Lord is when he is in prostration (Sujood).'",
            "Anas ibn Malik reported: The Prophet (ﷺ) was the most generous of people.",
            "\"Establish prayer, for indeed, prayer prohibits immorality and wrongdoing.\" (Al-Ankabut: 45)",
            "The best among you are those who learn the Quran and teach it."
        ];
        $spiritualLesson = $lessons[date('j') % count($lessons)];
        return view('tracker', compact('spiritualLesson'));
    })->name('tracker.index');
    Route::post('/tracker', [IbadahTrackerController::class, 'store']);

    // LMS Routes
    Route::get('/courses', function () {
        $courses = Course::where('is_archived', false)->latest()->get();
        return view('lms.index', compact('courses'));
    })->name('courses.catalog');

    Route::get('/lms', function () {
        $courses = Course::where('is_archived', false)->latest()->get();
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

    // Quizzes (Student)
    Route::get('/quizzes/{id}', [StudentQuizController::class, 'show'])->name('student.quizzes.show');
    Route::post('/quizzes/{id}/submit', [StudentQuizController::class, 'submit'])->name('student.quizzes.submit');
    Route::get('/quiz/{id}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quiz/{id}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');

    // Noor AI Routes
    Route::get('/noor-ai', function () {
        return view('noor-ai');
    })->name('noor.index');

    Route::post('/web-chat', [\App\Http\Controllers\Api\NoorAiController::class, 'sendMessage'])->name('web.chat');
    Route::get('/web-chat/history', [\App\Http\Controllers\Api\NoorAiController::class, 'getChatHistory'])->name('web.chat.history');

    // Quran Routes
    Route::get('/quran', [QuranController::class, 'index'])->name('quran.index');
    Route::get('/quran/surah/{id}', [QuranController::class, 'show'])->name('quran.show');
    Route::post('/quran/tadabbur/{ayahId}', [QuranController::class, 'saveTadabbur'])->name('quran.tadabbur.save');
    Route::post('/quran/ayah/{id}/read', [QuranController::class, 'markAyahAsRead'])->name('quran.ayah.read');
    Route::post('/quran/save-last-read', [QuranController::class, 'saveLastRead'])->name('quran.save_last_read');

    // Hadith Routes
    Route::get('/hadiths', [HadithController::class, 'index'])->name('hadiths.index');
    Route::get('/hadiths/chapter/{id}', [HadithController::class, 'chapter'])->name('hadiths.chapter');
    Route::get('/hadiths/category/{slug}', [HadithController::class, 'category'])->name('hadiths.category');
    Route::post('/hadiths/{id}/read', [HadithController::class, 'markAsRead'])->name('hadiths.read');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes (Protected by Auth & Admin Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Admin Course Management
    Route::post('/courses/{course}/archive', [CourseController::class, 'toggleArchive'])->name('courses.archive');
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

    // Admin Quiz Management
    Route::get('/quizzes', [AdminQuizController::class, 'index'])->name('quizzes.index');
    Route::delete('/quizzes/{quiz}', [AdminQuizController::class, 'destroy'])->name('quizzes.destroy');
    Route::get('/quizzes/create', [AdminQuizController::class, 'create'])->name('quizzes.create');
    Route::post('/quizzes/store', [AdminQuizController::class, 'store'])->name('quizzes.store');
    Route::get('/quizzes/{id}/edit', [AdminQuizController::class, 'edit'])->name('quizzes.edit');
    Route::put('/quizzes/{id}', [AdminQuizController::class, 'update'])->name('quizzes.update');

    // Admin Seerah Uploads
    Route::get('/seerah', [SeerahController::class, 'index'])->name('seerah.index');
    Route::get('/seerah/upload', [SeerahController::class, 'create'])->name('seerah.create');
    Route::post('/seerah/upload', [SeerahController::class, 'store'])->name('seerah.store');

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

    // Admin Hadith Management
    Route::get('/hadiths', [AdminHadithController::class, 'index'])->name('hadiths.index');
    Route::get('/hadiths/create', [AdminHadithController::class, 'create'])->name('hadiths.create');
    Route::post('/hadiths', [AdminHadithController::class, 'store'])->name('hadiths.store');
    Route::get('/hadiths/{id}/edit', [AdminHadithController::class, 'edit'])->name('hadiths.edit');
    Route::put('/hadiths/{id}', [AdminHadithController::class, 'update'])->name('hadiths.update');
    Route::delete('/hadiths/{id}', [AdminHadithController::class, 'destroy'])->name('hadiths.destroy');
});

// Database Connection Checker Route
Route::get('/check-db', function () {
    try {
        $dbName = DB::connection()->getDatabaseName();
        return "Laravel is currently connected to the database: " . $dbName;
    } catch (\Exception $e) {
        return "Connection Error: " . $e->getMessage();
    }
});

// Require authentication and verification routes
require __DIR__ . '/auth.php';