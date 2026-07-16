<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email | Tazkiyah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
</head>

<body class="font-['Inter'] antialiased bg-[#0c0a09] text-slate-300 min-h-screen flex items-center justify-center p-4">

    <div
        class="w-full max-w-md bg-[#1a1511]/80 backdrop-blur-xl border border-amber-500/20 p-8 rounded-3xl shadow-[0_0_40px_rgba(245,158,11,0.05)]">

        <div class="flex justify-center mb-6">
            <div
                class="h-12 w-12 bg-gradient-to-br from-amber-400 to-amber-600 text-slate-900 rounded-2xl flex items-center justify-center font-bold text-2xl shadow-lg shadow-amber-500/20">
                T
            </div>
        </div>

        <h2 class="text-2xl font-bold text-white text-center mb-4">Verify your email</h2>

        <p class="text-sm text-slate-400 text-center leading-relaxed mb-6">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link
            we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div
                class="mb-6 font-medium text-sm text-emerald-400 bg-emerald-500/10 p-4 rounded-xl border border-emerald-500/20 text-center shadow-sm">
                A new verification link has been sent to the email address you provided during registration.
            </div>
        @endif

        <div class="flex flex-col gap-4">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold py-3 px-5 rounded-xl shadow-[0_0_15px_rgba(245,158,11,0.2)] transition transform hover:-translate-y-0.5 text-center">
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="w-full text-center mt-2">
                @csrf
                <button type="submit"
                    class="text-sm text-slate-500 hover:text-amber-400 transition underline underline-offset-4">
                    Log Out
                </button>
            </form>
        </div>

    </div>

</body>

</html>