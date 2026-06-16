@extends('layouts.app')
@section('title', 'Edit Course')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-100 mt-10">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Edit Course</h2>
            <a href="{{ route('admin.courses.index') }}" class="text-indigo-600 font-bold hover:underline">← Back</a>
        </div>

        <form action="{{ route('admin.courses.update', $course->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-5">
                <label class="block font-bold text-gray-700 mb-2">Course Title</label>
                <input type="text" name="title" value="{{ $course->title }}"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 outline-none transition"
                    required>
            </div>

            <div class="mb-6">
                <label class="block font-bold text-gray-700 mb-2">Description</label>
                <textarea name="description"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 outline-none transition"
                    rows="4">{{ $course->description }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-700 transition shadow-sm">
                    Update Course
                </button>
            </div>
        </form>
    </div>
@endsection