@extends('layouts.app')
@section('title', $course->title)

@section('content')
<div class="max-w-5xl mx-auto mt-8">
    
    @php
        $totalLessons = 0;
        $completedCount = 0;
        
        foreach($course->modules as $module) {
            $totalLessons += $module->lessons->count();
            foreach($module->lessons as $lesson) {
                if(in_array($lesson->id, $completedLessonIds)) {
                    $completedCount++;
                }
            }
        }
        
        $progressPercentage = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;
    @endphp

    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $course->title }}</h1>
        <p class="text-gray-600 mb-6">{{ $course->description ?? 'Learn and grow with this comprehensive course.' }}</p>
        
        <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
            <div class="flex justify-between items-center mb-2">
                <span class="font-bold text-gray-700">Course Progress</span>
                <span class="font-bold text-emerald-600">{{ $progressPercentage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3 mb-2 overflow-hidden">
                <div class="bg-emerald-500 h-3 rounded-full transition-all duration-500" style="width: {{ $progressPercentage }}%"></div>
            </div>
            <p class="text-sm text-gray-500">You have completed {{ $completedCount }} out of {{ $totalLessons }} lessons.</p>
        </div>
    </div>

    <div class="space-y-6">
        @foreach($course->modules as $module)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-indigo-50 px-6 py-4 border-b border-indigo-100">
                    <h2 class="text-xl font-bold text-indigo-900">Module {{ $loop->iteration }}: {{ $module->title }}</h2>
                </div>
                
                <ul class="divide-y divide-gray-50">
                    @forelse($module->lessons as $lesson)
                        @php
                            $isCompleted = in_array($lesson->id, $completedLessonIds);
                        @endphp
                        
                        <li class="p-6 flex justify-between items-center hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                @if($isCompleted)
                                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-emerald-100 text-emerald-600 rounded-full">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </span>
                                @else
                                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-400 rounded-full">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    </span>
                                @endif
                                
                                <div>
                                    <h3 class="text-lg font-semibold {{ $isCompleted ? 'text-gray-500 line-through' : 'text-gray-800' }}">
                                        {{ $lesson->title }}
                                    </h3>
                                    <p class="text-sm text-gray-500 capitalize">{{ $lesson->content_type }} Content</p>
                                </div>
                            </div>

                            <a href="{{ url('/lesson/' . $lesson->id) }}" 
                               class="px-5 py-2 rounded-lg font-bold text-sm transition {{ $isCompleted ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm' }}">
                                {{ $isCompleted ? 'Review Lesson' : 'Start Lesson →' }}
                            </a>
                        </li>
                    @empty
                        <li class="p-6 text-gray-500 text-center">No lessons added to this module yet.</li>
                    @endforelse
                </ul>
            </div>
        @endforeach
    </div>
</div>
@endsection