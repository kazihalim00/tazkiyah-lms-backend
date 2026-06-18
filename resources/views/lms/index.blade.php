@extends('layouts.app')

@section('title', 'LMS Dashboard')
@section('header_title', 'Academy / LMS')

@section('content')
    <div class="max-w-6xl mx-auto space-y-10">

        <!-- Premium LMS Hero Banner -->
        <div class="relative bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 p-8 md:p-12 rounded-3xl shadow-xl overflow-hidden border border-indigo-950">
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
            <div class="relative z-10 max-w-xl space-y-4">
                <span class="bg-indigo-500/20 text-indigo-300 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider border border-indigo-500/30">
                    Tazkiyah Academy
                </span>
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight leading-tight">
                    Gain Knowledge That Benefitting Your Soul
                </h1>
                <p class="text-indigo-200 text-sm font-medium leading-relaxed">
                    Track your progress, complete interactive modules, and level up your spiritual intelligence through our curated courses.
                </p>
            </div>
        </div>

        <!-- Course Grid Section -->
        <div class="space-y-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-2">
                    <span class="w-2 h-7 bg-indigo-600 rounded-full"></span> Available Courses
                </h2>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                    {{ $courses->count() ?? 0 }} Total Courses
                </span>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($courses as $course)
                    @php
                        // Dynamic calculation of course completion percentage
                        $totalLessons = $course->lessons?->count() ?? 0;
                        $completedLessons = $course->completedLessonsByAuthUser?->count() ?? 0;
                        $progressPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
                    @endphp

                        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col transition duration-300 hover:shadow-md hover:border-indigo-100 group">

                            <!-- Course Thumbnail Placeholder / Image -->
                            <div class="relative h-44 bg-slate-100 overflow-hidden shrink-0">
                                @if($course->image)
                                    <img src="{{ asset('storage/' . $course->image) }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-105" alt="Course Cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-indigo-500/10 to-purple-500/10 flex items-center justify-center text-4xl">
                                        📚
                                    </div>
                                @endif
                                <span class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm text-indigo-700 text-[10px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wide shadow-sm border border-white">
                                    {{ $totalLessons }} Lessons
                                </span>
                            </div>

                            <!-- Course Details Body -->
                            <div class="p-6 flex-1 flex flex-col justify-between space-y-6">
                                <div class="space-y-2">
                                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition tracking-tight line-clamp-1">
                                        {{ $course->title }}
                                    </h3>
                                    <p class="text-gray-500 text-xs font-medium leading-relaxed line-clamp-2">
                                        {{ $course->description ?? 'No description available for this spiritual development module.' }}
                                    </p>
                                </div>

                                <!-- Premium Progress Bar Engine -->
                                <div class="space-y-2 bg-gray-50 p-4 rounded-2xl border border-gray-100/50">
                                    <div class="flex justify-between items-center text-xs font-bold">
                                        <span class="{{ $progressPercentage == 100 ? 'text-emerald-600' : 'text-gray-400' }}">
                                            {{ $progressPercentage == 100 ? 'Completed 🎉' : 'Course Progress' }}
                                        </span>
                                        <span class="text-indigo-600 font-black">{{ $progressPercentage }}%</span>
                                    </div>

                                    <!-- Track Bar Line Container -->
                                    <div class="w-full bg-gray-200/70 rounded-full h-2.5 overflow-hidden shadow-inner">
                                        <div class="h-2.5 rounded-full transition-all duration-500 ease-out {{ $progressPercentage == 100 ? 'bg-emerald-500' : 'bg-gradient-to-r from-indigo-500 to-indigo-600' }}"
                                             style="width: {{ $progressPercentage }}%">
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Launch Button -->
                                <a href="{{ route('lms.show', $course->id) }}" 
                                   class="w-full text-center font-black text-sm py-3.5 rounded-xl transition shadow-sm border flex items-center justify-center gap-2 {{ $progressPercentage > 0 ? 'bg-indigo-50 border-indigo-100 hover:bg-indigo-100 text-indigo-700' : 'bg-indigo-600 border-indigo-600 hover:bg-indigo-700 text-white' }}">
                                    <span>{{ $progressPercentage > 0 ? 'Continue Learning' : 'Start Course' }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                @empty
                    <!-- Empty State Block -->
                    <div class="col-span-full bg-white p-16 rounded-3xl border border-gray-100 text-center flex flex-col items-center justify-center">
                        <div class="h-16 w-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-2xl shadow-inner">
                            📖
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">No Courses Assigned</h3>
                        <p class="text-gray-500 font-medium text-sm max-w-xs">There are no study courses registered on your panel at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection