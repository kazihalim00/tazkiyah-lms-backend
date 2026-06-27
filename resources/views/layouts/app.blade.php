<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tazkiyah App')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-50 min-h-screen flex" x-data="{ mobileMenuOpen: false }">

    <div x-show="mobileMenuOpen" x-cloak x-transition.opacity @click="mobileMenuOpen = false"
        class="fixed inset-0 bg-black/40 z-20 md:hidden"></div>

    <aside
        class="w-64 bg-white border-r border-gray-100 flex flex-col fixed inset-y-0 left-0 h-full z-30 transform transition-transform duration-300 ease-in-out -translate-x-full md:translate-x-0"
        :class="{ 'translate-x-0': mobileMenuOpen }">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div
                    class="h-8 w-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold shadow-sm">
                    T
                </div>
                <span class="text-xl font-bold text-gray-800 tracking-tight">Tazkiyah</span>
            </div>

            <button @click="mobileMenuOpen = false" class="md:hidden text-gray-400 hover:text-gray-600 p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto" @click="mobileMenuOpen = false">

            <a href="{{ url('/my-dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200 {{ request()->is('my-dashboard') ? 'bg-gradient-to-r from-gray-900 to-indigo-800 text-white shadow-md' : 'text-gray-500 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                    </path>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ url('/tracker') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200 {{ request()->is('tracker') ? 'bg-gradient-to-r from-gray-900 to-indigo-800 text-white shadow-md' : 'text-gray-500 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                    </path>
                </svg>
                <span>Ibadah Tracker</span>
            </a>

            <a href="{{ route('quran.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200 {{ request()->routeIs('quran.*') ? 'bg-gradient-to-r from-gray-900 to-indigo-800 text-white shadow-md' : 'text-gray-500 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                    </path>
                </svg>
                <span>Al-Quran</span>
            </a>

            <a href="{{ route('hadiths.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200 {{ request()->routeIs('hadiths.*') ? 'bg-gradient-to-r from-gray-900 to-indigo-800 text-white shadow-md' : 'text-gray-500 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                    </path>
                </svg>
                <span>Hadith Corner</span>
            </a>

            <a href="{{ url('/noor-ai') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200 {{ request()->is('noor-ai') ? 'bg-gradient-to-r from-gray-900 to-indigo-800 text-white shadow-md' : 'text-gray-500 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <span>Noor AI</span>
            </a>

            <a href="{{ route('courses.catalog') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200 {{ request()->routeIs('courses.*') ? 'bg-gradient-to-r from-gray-900 to-indigo-800 text-white shadow-md' : 'text-gray-500 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222">
                    </path>
                </svg>
                <span>Courses (LMS)</span>
            </a>

            <a href="{{ route('feed.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200 {{ Request::is('feed*') ? 'bg-gradient-to-r from-gray-900 to-indigo-800 text-white shadow-md' : 'text-gray-500 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                    </path>
                </svg>
                <span>Community Feed</span>
            </a>

            <a href="{{ route('leaderboard.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200 {{ Request::is('leaderboard*') ? 'bg-gradient-to-r from-gray-900 to-indigo-800 text-white shadow-md' : 'text-gray-500 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                    </path>
                </svg>
                <span>Leaderboard</span>
            </a>

            @php
                $totalUnreadMessages = 0;
                if (auth()->check()) {
                    $totalUnreadMessages = \App\Models\PartnerMessage::where('receiver_id', auth()->id())
                        ->where('is_read', false)
                        ->count();
                }
            @endphp
            <a href="{{ route('chat.index') }}"
                class="flex items-center justify-between px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200 {{ Request::is('messages*') ? 'bg-gradient-to-r from-gray-900 to-indigo-800 text-white shadow-md' : 'text-gray-500 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                        </path>
                    </svg>
                    <span>Messages</span>
                </div>
                @if($totalUnreadMessages > 0)
                    <span
                        class="bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full animate-pulse shadow-sm">
                        {{ $totalUnreadMessages }}
                    </span>
                @endif
            </a>

            <a href="{{ route('donate.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200 {{ Request::is('donate*') ? 'bg-gradient-to-r from-gray-900 to-indigo-800 text-white shadow-md' : 'text-gray-500 hover:bg-indigo-50 hover:text-indigo-700' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                    </path>
                </svg>
                <span>Donate / Sadaqah</span>
            </a>

            @if(auth()->check() && auth()->user()->is_admin == 1)
                <div class="mt-6 mb-2 px-6 text-[10px] font-black uppercase tracking-wider text-gray-400">
                    Control Center
                </div>

                <a href="{{ route('admin.courses.index') }}"
                    class="flex items-center gap-3 px-6 py-3 text-sm font-bold text-rose-600 hover:bg-rose-50 border-r-4 border-transparent hover:border-rose-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Admin Panel
                </a>
            @endif
        </nav>

        <div class="p-4 border-t border-gray-100">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-xl transition font-bold text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 md:ml-64 flex flex-col min-h-screen w-full">
        <header
            class="bg-white border-b border-gray-100 p-4 md:px-8 flex justify-between items-center sticky top-0 z-10">
            <div class="flex items-center gap-3 min-w-0">
                <button @click="mobileMenuOpen = true"
                    class="md:hidden -ml-2 p-2 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-indigo-600 transition shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="text-gray-800 font-bold text-lg truncate">
                    @yield('header_title', 'Dashboard')
                </div>
            </div>

            <div class="relative shrink-0" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-3 hover:opacity-80 transition">
                    <div class="hidden sm:flex items-center gap-2">
                        <span class="font-bold text-gray-700">{{ auth()->user()->name ?? 'User' }}</span>

                        @if(auth()->check() && auth()->user()->is_admin == 1)
                            <span
                                class="bg-rose-100 text-rose-700 text-[10px] px-2 py-0.5 rounded-md font-black uppercase tracking-wide border border-rose-200">
                                Admin
                            </span>
                        @else
                            <span
                                class="bg-indigo-50 text-indigo-600 text-[10px] px-2 py-0.5 rounded-md font-black uppercase tracking-wide border border-indigo-100">
                                Member
                            </span>
                        @endif
                    </div>

                    @if(auth()->check() && auth()->user()->image)
                        <img src="{{ auth()->user()->image_url }}" class="h-10 w-10 rounded-full object-cover"
                            alt="Profile Photo">
                    @else
                        <div
                            class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-black">
                            {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'U' }}
                        </div>
                    @endif
                </button>

                <div x-show="open" @click.away="open = false" x-cloak
                    class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">

                    <a href="{{ route('profile') }}"
                        class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-indigo-50 transition">
                        Edit Profile
                    </a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        @if(session('success'))
            <div class="px-4 md:px-8 pt-4 md:pt-6">
                <div
                    class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded shadow-sm font-semibold">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="p-4 md:p-8 flex-1 w-full max-w-full overflow-x-hidden">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>

</html>