@extends('layouts.app')
@section('title', 'LMS - Tazkiyah')
@section('header_title', 'My Courses')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Learning Path 📚</h1>
        <p class="text-gray-500 mt-2">Access your spiritual and technical knowledge base.</p>
    </div>

    <!-- Course Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($courses as $course)
            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition p-6 flex flex-col">
                <h3 class="text-lg font-bold text-gray-800">{{ $course->title }}</h3>
                <p class="text-sm text-gray-500 mt-2 mb-6 flex-1">{{ $course->description }}</p>

                <a href="{{ url('/lms/' . $course->id) }}"
                    class="inline-block text-center bg-indigo-600 text-white px-4 py-3 rounded-xl text-sm font-bold hover:bg-indigo-700 transition">
                    View Modules
                </a>
            </div>
        @endforeach
    </div>
@endsection