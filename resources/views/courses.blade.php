@extends('layouts.app')
@section('title', 'All Courses')

@section('content')
    <div class="max-w-6xl mx-auto mt-10 px-4">
        <!-- Page Header -->
        <div class="mb-10 text-center">
            <h1 class="text-4xl font-bold text-gray-800 mb-3">Explore Our Courses</h1>
            <p class="text-gray-600 text-lg">Enhance your knowledge with our comprehensive courses.</p>
        </div>

        <!-- Courses Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($courses as $course)
                <div
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition duration-300 flex flex-col">
                    <!-- Card Image/Icon Placeholder -->
                    <div class="h-48 bg-indigo-50 border-b border-indigo-100 flex items-center justify-center">
                        <span class="text-6xl">📚</span>
                    </div>

                    <!-- Card Content -->
                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $course->title }}</h3>
                        <p class="text-gray-600 mb-6 flex-1">
                            {{ \Illuminate\Support\Str::limit($course->description ?? 'No description available for this course.', 100) }}
                        </p>

                        <!-- View Course Button -->
                        <a href="{{ url('/lms/' . $course->id) }}"
                            class="mt-auto block w-full text-center bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-700 transition shadow-sm">
                            View Course →
                        </a>
                    </div>
                </div>
            @empty
                <!-- No Courses State -->
                <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-5xl mb-4 block">🏜️</span>
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">No Courses Available Yet</h3>
                    <p class="text-gray-500">Check back later for new and exciting content!</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection