<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\IbadahTracker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Models\ChatLog;


// Redirect root URL to login page
Route::get('/', function () {
    return redirect('/login');
});

// Show Login Page
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Process Login Request
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

// Show Registration Page
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Process Registration Request
Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed'
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'user',
        'total_points' => 0,
    ]);

    Auth::login($user);
    return redirect('/my-dashboard');
});

// Process Logout Request
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Secured Dashboard Route
Route::get('/my-dashboard', function () {
    // Get the currently logged-in user dynamically!
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
})->middleware('auth'); // <-- Protects this route so only logged-in users can see it
// Noor AI Web Chat Route
Route::post('/web-chat', function (Request $request) {
    $request->validate(['message' => 'required|string']);
    $user = Auth::user();
    $userMessage = $request->message;

    // Save user message
    $chatLog = ChatLog::create([
        'user_id' => $user->id,
        'user_message' => $userMessage,
    ]);

    try {
        // Send request to Python Flask server
        $response = Http::post('http://127.0.0.1:5000/api/chat', [
            'message' => $userMessage
        ]);

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
        $chatLog->update(['ai_response' => 'Connection to Noor AI failed. Please ensure the Python server is running.']);
    }

    return response()->json([
        'success' => true,
        'reply' => $chatLog->ai_response
    ]);
})->middleware('auth');