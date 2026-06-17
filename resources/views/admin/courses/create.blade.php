@extends('layouts.app')
@section('title', 'Add New Course')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-3xl shadow-sm border border-gray-100 mt-10">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-black text-gray-900">Add New Course</h2>
                <p class="text-xs text-gray-400 mt-0.5">Setup a new spiritual progression module</p>
            </div>
            <a href="{{ route('admin.courses.index') }}" class="text-indigo-600 font-bold text-sm hover:underline">← Back to
                Courses</a>
        </div>

        <form action="{{ route('admin.courses.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block font-bold text-xs uppercase tracking-wider text-gray-400 mb-2">Course Title</label>
                <input type="text" name="title"
                    class="w-full border border-gray-200 rounded-xl p-3.5 focus:ring-2 focus:ring-indigo-500 outline-none transition font-medium text-gray-800"
                    required placeholder="e.g. Introduction to Islamic Studies">
            </div>

            <div>
                <label class="block font-bold text-xs uppercase tracking-wider text-gray-400 mb-2">Target Course
                    Level</label>
                <select name="level" required
                    class="w-full border border-gray-200 rounded-xl p-3.5 bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition font-medium text-gray-800">
                    <option value="" disabled selected>Select course complexity level</option>
                    <option value="Beginner">Beginner (General Knowledge)</option>
                    <option value="Intermediate">Intermediate (Deep Dive)</option>
                    <option value="Advanced">Advanced (Scholarly/Tazkiyah Intensive)</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-xs uppercase tracking-wider text-gray-400 mb-2">Description
                    (Optional)</label>
                <textarea name="description"
                    class="w-full border border-gray-200 rounded-xl p-3.5 focus:ring-2 focus:ring-indigo-500 outline-none transition font-medium text-gray-800"
                    rows="4" placeholder="Briefly describe the context of this course..."></textarea>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-50">
                <button type="submit"
                    class="bg-indigo-600 text-white px-8 py-3.5 rounded-xl font-black text-xs uppercase tracking-wider hover:bg-indigo-700 transition shadow-md shadow-indigo-100">
                    🚀 Save & Publish Course
                </button>
            </div>
        </form>
    </div>
@endsection