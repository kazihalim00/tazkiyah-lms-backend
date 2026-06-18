@extends('layouts.app')

@section('title', 'Courses - Tazkiyah')
@section('header_title', 'Course Catalog')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Explore Courses</h1>
        <p class="text-gray-500 mt-2">Enhance your knowledge and spiritual journey.</p>
    </div>

    @if($courses->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm p-12 border border-gray-100 text-center max-w-2xl mx-auto mt-10">
            <div class="h-20 w-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">No Courses Available Yet</h3>
            <p class="text-gray-500">We are working hard to bring you new learning materials. Please check back later!</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
                <div
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition duration-300 flex flex-col group">

                    <div
                        class="h-48 bg-gradient-to-br from-indigo-500 via-purple-500 to-indigo-600 flex items-center justify-center relative overflow-hidden">

                        <div
                            class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-indigo-700 text-[10px] font-black px-3 py-1 rounded-full shadow-sm uppercase tracking-wider">
                            {{ $course->modules->sum(function ($module) {
                        return $module->lessons->count(); }) }} LESSONS
                        </div>

                        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]">
                        </div>

                        <span class="text-white font-bold text-xl opacity-95 px-6 text-center z-10">{{ $course->title }}</span>

                        <div
                            class="absolute top-4 right-4 bg-emerald-500 text-white text-[11px] font-extrabold px-3 py-1.5 rounded-full z-10 shadow-md uppercase tracking-wider">
                            Enroll Now
                        </div>
                    </div>

                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-800 mb-2 line-clamp-2 group-hover:text-indigo-600 transition">
                            {{ $course->title }}
                        </h3>
                        <p class="text-gray-500 text-sm mb-6 line-clamp-3 leading-relaxed">
                            {{ $course->description ?? 'Join this course to enhance your spiritual understanding and practical knowledge. Step by step learning modules designed for everyone.' }}
                        </p>

                        <div class="mt-auto">
                            <a href="{{ url('/lms/' . $course->id) }}"
                                class="block w-full text-center bg-indigo-50 text-indigo-700 font-semibold py-3 rounded-xl hover:bg-indigo-600 hover:text-white transition duration-300">
                                View Course Details
                            </a>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    @endif
@endsection