@extends('layouts.app')

@section('title', $course->title . ' - Tazkiyah LMS')
@section('header_title', 'Course Details')

@section('content')
<div class="max-w-5xl mx-auto">
    
    <!-- Back Button -->
    <a href="{{ route('courses.catalog') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-600 transition mb-6 font-medium">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Back to Courses
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Side: Course Info -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Course Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="h-40 bg-gradient-to-br from-indigo-500 via-purple-500 to-indigo-600 flex items-center justify-center relative">
                    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                    <svg class="w-16 h-16 text-white opacity-90 z-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                    </svg>
                </div>
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-gray-800 mb-3">{{ $course->title }}</h1>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">
                        {{ $course->description ?? 'No description provided for this course yet.' }}
                    </p>
                    
                    <div class="border-t border-gray-100 pt-4">
                        <div class="flex justify-between items-center text-sm mb-2">
                            <span class="text-gray-500 font-medium">Course Progress</span>
                            @php
                                $totalLessons = $course->modules ? $course->modules->flatMap->lessons->count() : 0;
                                $completedCount = count($completedLessonIds ?? []);
                                $progress = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;
                            @endphp
                            <span class="text-indigo-600 font-bold">{{ $progress }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Modules & Lessons -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Course Curriculum</h2>

                @if($course->modules && $course->modules->count() > 0)
                    <div class="space-y-6">
                        @foreach($course->modules as $index => $module)
                            <div class="border border-gray-100 rounded-xl overflow-hidden">
                                <!-- Module Header -->
                                <div class="bg-gray-50 px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                                    <h3 class="font-bold text-gray-800">Module {{ $index + 1 }}: {{ $module->title }}</h3>
                                    <span class="text-xs font-medium text-gray-500 bg-white px-2.5 py-1 rounded-md border border-gray-200">
                                        {{ $module->lessons->count() }} Lessons
                                    </span>
                                </div>
                                
                                <!-- Lessons List -->
                                <div class="divide-y divide-gray-100 bg-white">
                                    @forelse($module->lessons as $lesson)
                                        @php
                                            $isCompleted = in_array($lesson->id, $completedLessonIds ?? []);
                                        @endphp
                                        <div class="px-5 py-4 flex items-center justify-between hover:bg-gray-50 transition group">
                                            <div class="flex items-center gap-3">
                                                <!-- Status Icon -->
                                                @if($isCompleted)
                                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                                    </div>
                                                @else
                                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center group-hover:bg-indigo-100 group-hover:text-indigo-500 transition">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" /></svg>
                                                    </div>
                                                @endif
                                                
                                                <span class="{{ $isCompleted ? 'text-gray-500 line-through' : 'text-gray-700 font-medium' }}">{{ $lesson->title }}</span>
                                            </div>
                                            
                                            <a href="{{ url('/lesson/' . $lesson->id) }}" class="text-sm font-semibold {{ $isCompleted ? 'text-emerald-600 hover:text-emerald-700' : 'text-indigo-600 hover:text-indigo-700' }}">
                                                {{ $isCompleted ? 'Review' : 'Start' }}
                                            </a>
                                        </div>
                                    @empty
                                        <div class="px-5 py-4 text-sm text-gray-500 italic">No lessons added to this module yet.</div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <p class="text-gray-500">Curriculum is currently being updated. Check back soon!</p>
                    </div>
                @endif
                
            </div>
        </div>
        
    </div>
</div>
@endsection