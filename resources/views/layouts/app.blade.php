<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tazkiyah App')</title>
    <!-- CSS and JS Dependencies -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-50 min-h-screen flex">

    <!-- Left Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-100 flex-col hidden md:flex fixed h-full z-20">
        <div class="p-6 border-b border-gray-100 flex items-center gap-3">
            <div
                class="h-8 w-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold shadow-sm">
                T</div>
            <span class="text-xl font-bold text-gray-800 tracking-tight">Tazkiyah</span>
        </div>

        <nav class="flex-1 p-4 space-y-1">
            <a href="{{ url('/my-dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->is('my-dashboard') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">Dashboard</a>
            <a href="{{ url('/tracker') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->is('tracker') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">Ibadah
                Tracker</a>
            <a href="{{ url('/noor-ai') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->is('noor-ai') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">Noor
                AI</a>
            <a href="{{ route('courses.catalog') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('courses.catalog') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">Courses
                (LMS)</a>
        </nav>

        <div class="p-4 border-t border-gray-100">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-xl transition font-medium">Logout</button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <header
            class="bg-white border-b border-gray-100 p-4 md:px-8 flex justify-between items-center sticky top-0 z-10">
            <div class="text-gray-800 font-semibold text-lg">@yield('header_title', 'Dashboard')</div>

            <!-- Profile Dropdown with Image -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-3 hover:opacity-80 transition">
                    <span class="font-bold text-gray-700 hidden sm:block">{{ auth()->user()->name ?? 'User' }}</span>
                    @if(auth()->user() && auth()->user()->image)
                        <img src="{{ asset('storage/' . auth()->user()->image) }}"
                            class="h-10 w-10 rounded-full object-cover border-2 border-indigo-100 shadow-sm">
                    @else
                        <div
                            class="h-10 w-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold uppercase">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                    @endif
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                    class="absolute right-0 mt-3 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                    <a href="{{ route('profile') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">Edit Profile</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Success Message -->
        @if(session('success'))
            <div class="px-8 pt-6">
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="p-8 flex-1">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>

</html>