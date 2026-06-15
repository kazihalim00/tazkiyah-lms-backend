@extends('layouts.app')
@section('title', $course->title)
@section('header_title', 'Course Details')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Course Header -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 mb-8">
            <h1 class="text-4xl font-bold text-gray-800">{{ $course->title }}</h1>
            <p class="text-gray-500 mt-4 text-lg leading-relaxed">{{ $course->description }}</p>
        </div>

        <!-- Modules Section -->
        <div class="space-y-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Course Modules</h2>

            @forelse($course->modules as $module)
                <div
                    class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between hover:border-indigo-200 transition">
                    <div class="flex items-center gap-4">
                        <div
                            class="h-10 w-10 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center font-bold">
                            {{ $module->order }}
                        </div>
                        <span class="font-semibold text-gray-700">{{ $module->title }}</span>
                    </div>

                    @if(isset($module->lessons) && $module->lessons->count() > 0)
                        <a href="{{ url('/lesson/' . $module->lessons->first()->id) }}"
                            class="text-indigo-600 font-bold hover:underline">
                            Start Lesson →
                        </a>
                    @else
                        <span class="text-gray-400 text-sm">No lessons yet</span>
                    @endif
                </div>
            @empty
                <div class="text-center py-10 text-gray-500">No modules found for this course.</div>
            @endforelse
        </div>
    </div>
@endsection