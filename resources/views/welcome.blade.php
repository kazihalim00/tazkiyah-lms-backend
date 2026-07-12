<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tazkiyah | Your Islamic Ecosystem</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .arabic-font {
            font-family: 'Amiri', serif;
        }

        .bento-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .bento-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -15px rgba(245, 158, 11, 0.15);
            border-color: rgba(245, 158, 11, 0.3);
        }

        /* 🟢 EXACT CALLIGRAPHY BACKGROUND 🟢 */
        body {
            background-color: #0c0a09;
            background-image:
                /* Left dark gradient for text readability */
                linear-gradient(to right, rgba(12, 10, 9, 0.98) 0%, rgba(12, 10, 9, 0.85) 45%, rgba(12, 10, 9, 0.1) 100%),
                /* Top & bottom fade */
                linear-gradient(to bottom, rgba(12, 10, 9, 0.6) 0%, transparent 15%, transparent 85%, rgba(12, 10, 9, 0.95) 100%),
                url('{{ asset("images/calligraphy-bg.jpg") }}');
            background-size: cover;
            background-position: right center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }

        /* Adjusted glass panel */
        .glass-panel {
            background: rgba(20, 18, 16, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(245, 158, 11, 0.15);
        }

        /* 🟢 NEW: Smooth Up-Down Floating Animation 🟢 */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        .animate-float {
            animation: float 4s ease-in-out infinite;
        }
    </style>
</head>

<body
    class="antialiased font-['Inter'] selection:bg-amber-500 selection:text-slate-900 overflow-x-hidden text-slate-200">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 top-0 left-0 glass-panel border-b-0 shadow-lg shadow-black/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3">
                    <div
                        class="h-10 w-10 bg-gradient-to-br from-amber-400 to-amber-600 text-slate-900 rounded-xl flex items-center justify-center font-bold shadow-lg shadow-amber-500/20">
                        <span class="text-xl">T</span>
                    </div>
                    <span class="text-2xl font-extrabold text-white tracking-tight">Tazkiyah</span>
                </div>
                <div class="flex items-center gap-5">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="text-sm font-semibold text-amber-400 hover:text-amber-300 transition">Dashboard
                                &rarr;</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-sm font-medium text-slate-300 hover:text-white transition hidden sm:block">Log
                                in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="bg-amber-500 hover:bg-amber-400 text-slate-900 font-bold text-sm py-2.5 px-5 rounded-xl shadow-[0_0_15px_rgba(245,158,11,0.3)] transition transform hover:-translate-y-0.5">Get
                                    Started</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row items-center gap-12">

            <!-- Left Content -->
            <div class="lg:w-3/5 text-left z-10">
                <div
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-black/40 border border-amber-500/20 text-amber-400 text-sm font-medium mb-8 backdrop-blur-md shadow-lg shadow-amber-500/5">
                    <span class="arabic-font text-lg leading-none pt-1 text-amber-300">بِسْمِ اللَّهِ الرَّحْمَٰنِ
                        الرَّحِيمِ</span>
                </div>

                <h1 class="text-5xl md:text-7xl font-extrabold text-white tracking-tight mb-6 leading-[1.1]">
                    Purify your heart. <br />
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-400 to-amber-600">Elevate
                        your Deen.</span>
                </h1>

                <p class="text-lg md:text-xl text-slate-300 max-w-2xl leading-relaxed mb-10 font-light drop-shadow-md">
                    Tazkiyah is your private digital ecosystem. Track your daily Ibadah, find righteous accountability
                    partners, and get 24/7 spiritual guidance from Noor AI—all in a strictly gender-segregated safe
                    space.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                    <a href="{{ route('register') }}"
                        class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-900 font-bold text-lg py-4 px-8 rounded-2xl shadow-[0_0_30px_rgba(245,158,11,0.25)] transition transform hover:-translate-y-1 text-center flex items-center justify-center gap-2">
                        Start Your Journey
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <a href="#explore"
                        class="bg-black/40 hover:bg-black/60 text-white border border-white/10 font-semibold text-lg py-4 px-8 rounded-2xl transition backdrop-blur-md text-center">
                        Explore Features
                    </a>
                </div>
            </div>

            <!-- Right Graphic (Floating App Preview) -->
            <div class="lg:w-2/5 relative hidden lg:block z-10 perspective-1000">
                <div
                    class="relative w-full rounded-3xl overflow-hidden glass-panel border border-white/10 shadow-2xl transform rotate-y-[-10deg] rotate-x-[5deg] hover:rotate-y-0 hover:rotate-x-0 transition-transform duration-700 ease-out group">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-amber-500/10 to-transparent opacity-50 group-hover:opacity-100 transition-opacity">
                    </div>

                    <!-- Decorative Mockup Content inside the float -->
                    <div class="p-5 flex flex-col gap-4 relative z-10">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-8 h-8 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold border border-amber-500/30">
                                    🌙</div>
                                <span class="font-bold text-white">Noor AI</span>
                            </div>
                            <span
                                class="text-[10px] uppercase font-bold bg-emerald-500/20 text-emerald-400 px-2 py-1 rounded">Online</span>
                        </div>

                        <div
                            class="bg-white/10 p-3.5 rounded-2xl rounded-tr-sm border border-white/5 self-end max-w-[85%] backdrop-blur-sm">
                            <p class="text-[13px] text-slate-200">How do I build consistency in my prayers?</p>
                        </div>
                        <div
                            class="bg-black/50 p-4 rounded-2xl rounded-tl-sm border border-amber-500/20 self-start max-w-[95%] shadow-lg shadow-amber-500/5 backdrop-blur-sm">
                            <p class="text-[13px] text-slate-300 leading-relaxed">Consistency comes from small, unbroken
                                habits. The Prophet (ﷺ) said, "The most beloved of deeds to Allah are those that are
                                most consistent, even if it is small." Let's start by tracking just your Fardh prayers
                                this week in the Ibadah Tracker.</p>
                        </div>

                        <div
                            class="mt-2 bg-black/40 p-4 rounded-2xl border border-white/10 flex items-center justify-between backdrop-blur-sm shadow-inner">
                            <div>
                                <h4 class="text-xs font-bold text-white mb-2 uppercase tracking-wide">Today's Progress
                                </h4>
                                <div class="flex gap-1.5">
                                    <div
                                        class="w-6 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]">
                                    </div>
                                    <div
                                        class="w-6 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]">
                                    </div>
                                    <div
                                        class="w-6 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]">
                                    </div>
                                    <div class="w-6 h-1.5 rounded-full bg-white/10"></div>
                                    <div class="w-6 h-1.5 rounded-full bg-white/10"></div>
                                </div>
                            </div>
                            <div class="text-amber-400 font-black text-2xl">60%</div>
                        </div>
                    </div>
                </div>

                <!-- 🟢 Floating Elements - Moved down further and added animate-float 🟢 -->
                <div
                    class="absolute -bottom-14 left-6 bg-[#0a0807]/95 p-4 rounded-2xl border border-amber-500/30 shadow-2xl shadow-black/60 flex items-center gap-3 backdrop-blur-md z-30 animate-float">
                    <span class="text-2xl drop-shadow-md">🏆</span>
                    <div>
                        <p class="text-[11px] text-amber-500/90 font-bold uppercase tracking-wider">Ranked Up!</p>
                        <p class="text-sm font-bold text-white drop-shadow-sm">Mubtadi Level</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Inside the Dashboard (Bento Grid) -->
    <div id="explore" class="py-24 relative z-10 bg-[#0a0807]/95 backdrop-blur-xl border-t border-amber-500/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-amber-500 font-semibold tracking-wider uppercase text-sm mb-2 block">Complete
                    Ecosystem</span>
                <h2 class="text-3xl md:text-5xl font-bold text-white mb-4">Everything you need, in one place.</h2>
                <p class="mt-2 text-slate-400 max-w-2xl mx-auto text-lg">Designed meticulously to remove distractions
                    and keep you focused on your Akhirah.</p>
            </div>

            <!-- Bento Grid UI -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- 1. Ibadah Tracker (Spans 1 column) -->
                <div
                    class="bento-card lg:col-span-1 glass-panel rounded-3xl overflow-hidden flex flex-col relative group">
                    <div class="p-8 pb-4 z-10">
                        <div
                            class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-xl flex items-center justify-center mb-6 border border-emerald-500/20">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Ibadah Tracker</h3>
                        <p class="text-sm text-slate-400 mb-6">Build unbreakable habits by logging your daily prayers,
                            fasting, and good deeds.</p>
                    </div>
                    <div class="px-8 pb-8 mt-auto z-10">
                        <div class="space-y-3">
                            <div
                                class="bg-white/5 p-3 rounded-xl border border-white/10 flex items-center justify-between">
                                <span class="font-medium text-slate-200">Fajr</span>
                                <div class="w-5 h-5 rounded-md bg-emerald-500 flex items-center justify-center"><svg
                                        class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg></div>
                            </div>
                            <div
                                class="bg-white/5 p-3 rounded-xl border border-white/10 flex items-center justify-between">
                                <span class="font-medium text-slate-200">Dhuhr</span>
                                <div class="w-5 h-5 rounded-md bg-emerald-500 flex items-center justify-center"><svg
                                        class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Noor AI Mentor (Spans 2 columns) -->
                <div
                    class="bento-card lg:col-span-2 glass-panel rounded-3xl overflow-hidden flex flex-col relative group">
                    <div class="p-8 pb-0 z-10 flex justify-between items-start">
                        <div>
                            <div
                                class="w-12 h-12 bg-amber-500/10 text-amber-400 rounded-xl flex items-center justify-center mb-6 border border-amber-500/20 font-bold text-xl">
                                🌙</div>
                            <h3 class="text-xl font-bold text-white mb-2">Noor AI Companion</h3>
                            <p class="text-sm text-slate-400 mb-6 max-w-md">Your empathetic digital mentor. It remembers
                                your struggles, provides non-judgmental support, and answers Deeni questions based on
                                Quran & Sunnah.</p>
                        </div>
                        <span
                            class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-medium text-slate-300">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Core Memory Active
                        </span>
                    </div>

                    <div class="bg-black/30 p-8 pt-6 flex-1 mt-auto border-t border-white/5 flex flex-col justify-end">
                        <div class="space-y-4 max-w-xl w-full">
                            <div class="flex justify-end">
                                <div
                                    class="bg-white/10 text-white text-sm py-3 px-5 rounded-2xl rounded-tr-sm shadow-sm border border-white/5">
                                    I relapsed again today. I feel so ashamed...
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div
                                    class="h-8 w-8 bg-gradient-to-br from-amber-400 to-amber-600 rounded-full flex items-center justify-center shrink-0 font-bold text-slate-900 shadow-lg">
                                    N</div>
                                <div
                                    class="bg-amber-500/10 border border-amber-500/20 text-slate-200 text-sm py-3 px-5 rounded-2xl rounded-tl-sm shadow-sm leading-relaxed">
                                    I know the guilt is heavy, but remember Allah's promise: <em>"Do not despair of the
                                        mercy of Allah..."</em> [Surah Az-Zumar: 53]. You are struggling, but you are
                                    trying. Let's reset your tracker and start fresh right now.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Dynamic Global Leaderboard (Spans 1 column) -->
                <div class="bento-card lg:col-span-1 glass-panel rounded-3xl overflow-hidden flex flex-col relative">
                    <div class="p-8 pb-4 border-b border-white/5 flex justify-between items-center z-10">
                        <div>
                            <h3 class="text-xl font-bold text-white mb-1">Leaderboard</h3>
                            <p class="text-sm text-slate-400">Compete in good deeds</p>
                        </div>
                        <div
                            class="w-10 h-10 bg-indigo-500/10 text-indigo-400 rounded-full flex items-center justify-center border border-indigo-500/20 text-xl">
                            🏆</div>
                    </div>
                    <div class="p-6 flex flex-col gap-3 flex-1 bg-black/20">
                        @if(isset($topUsers))
                            @foreach($topUsers as $index => $user)
                                <div
                                    class="bg-white/5 p-3.5 rounded-xl border {{ $index === 0 ? 'border-amber-500/30 shadow-[0_0_15px_rgba(245,158,11,0.1)]' : 'border-white/5' }} flex items-center justify-between transition hover:bg-white/10">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="font-bold {{ $index === 0 ? 'text-amber-400' : 'text-slate-500' }} text-sm w-4">#{{ $index + 1 }}</span>
                                        <div
                                            class="h-9 w-9 {{ $index === 0 ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-white/5 text-slate-300' }} rounded-full flex items-center justify-center text-xs font-bold uppercase shadow-sm">
                                            {{ $user->initials }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-white leading-tight">{{ $user->name }}</p>
                                            <p class="text-[11px] text-emerald-400 font-medium mt-0.5">{{ $user->points }} pts
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- 4. Community Feed Preview (Spans 1 column) -->
                <div class="bento-card lg:col-span-1 glass-panel rounded-3xl overflow-hidden flex flex-col relative">
                    <div class="p-8 pb-4 z-10">
                        <div
                            class="w-12 h-12 bg-blue-500/10 text-blue-400 rounded-xl flex items-center justify-center mb-6 border border-blue-500/20">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Community Feed</h3>
                        <p class="text-sm text-slate-400">Share reflections and find accountability partners.</p>
                    </div>
                    <div class="px-6 pb-6 mt-auto">
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/10 shadow-sm">
                            <div class="flex items-center gap-3 mb-3">
                                <div
                                    class="h-8 w-8 bg-indigo-500/20 text-indigo-300 font-bold rounded-full flex items-center justify-center text-xs border border-indigo-500/30">
                                    IA</div>
                                <div>
                                    <h4 class="text-xs font-bold text-white">Ishtiaque Ahmed</h4>
                                    <span class="text-[9px] text-slate-400">2h ago</span>
                                </div>
                            </div>
                            <p class="text-xs text-slate-300 mb-3 line-clamp-3">Alhamdulillah, completed the Seerah
                                module today. Highly recommend it to everyone here! ✨</p>
                            <div class="flex items-center gap-4 text-slate-400 border-t border-white/5 pt-2">
                                <span class="flex items-center gap-1 text-[10px] hover:text-white cursor-pointer"><svg
                                        class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364z">
                                        </path>
                                    </svg> 24</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. Modesty Assured / Segregation (Spans 1 column) -->
                <div
                    class="bento-card lg:col-span-1 bg-gradient-to-br from-[#1a1511] to-[#0c0a09] rounded-3xl overflow-hidden flex flex-col text-white shadow-xl relative border border-amber-500/20 group">
                    <div class="p-8 flex-1 flex flex-col justify-center items-center text-center z-10">
                        <div
                            class="h-20 w-20 bg-amber-500/10 rounded-2xl flex items-center justify-center mb-6 border border-amber-500/20 shadow-[0_0_30px_rgba(245,158,11,0.1)] group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-white">100% Modesty Assured</h3>
                        <p class="text-amber-200/70 text-sm leading-relaxed">Our platform enforces strict gender
                            segregation at the database level. Brothers only interact with brothers, and sisters with
                            sisters.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Final CTA -->
    <div class="relative py-24 border-t border-white/10 overflow-hidden">
        <div class="absolute inset-0 bg-[#0c0a09]/95 backdrop-blur-xl"></div>
        <div class="relative max-w-3xl mx-auto px-4 text-center z-10">
            <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-6">Ready to organize your Deen?</h2>
            <p class="text-slate-400 mb-10 text-lg">Join Tazkiyah today and take the first step towards a consistent and
                mindful Islamic lifestyle.</p>
            <a href="{{ route('register') }}"
                class="inline-block bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-lg py-4 px-12 rounded-2xl shadow-[0_0_30px_rgba(245,158,11,0.3)] transition transform hover:scale-105">
                Create Your Account Now
            </a>
            <p class="mt-6 text-sm text-slate-500">Already have an account? <a href="{{ route('login') }}"
                    class="text-amber-400 hover:text-amber-300 underline underline-offset-4">Sign in</a></p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-[#050404] border-t border-white/5 py-10 relative z-10">
        <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row items-center justify-between">
            <div class="flex items-center gap-2 mb-4 md:mb-0">
                <div class="h-6 w-6 bg-amber-500 text-slate-950 rounded flex items-center justify-center font-bold">
                    <span class="text-[10px]">T</span>
                </div>
                <span class="text-white font-bold tracking-tight">Tazkiyah</span>
            </div>
            <p class="text-slate-500 text-sm">
                &copy; {{ date('Y') }} Tazkiyah Platform. Architected by <span class="text-slate-300 font-medium">Kazi
                    Abdul Halim Sunny</span>.
            </p>
        </div>
    </footer>

</body>

</html>