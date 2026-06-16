@extends('layouts.app')
@section('title', 'Edit Lesson')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-100 mt-10">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Edit Lesson</h2>
            <a href="{{ route('admin.lessons.index') }}" class="text-indigo-600 font-bold hover:underline">← Back</a>
        </div>

        <form action="{{ route('admin.lessons.update', $lesson->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Module Selection -->
            <div class="mb-5">
                <label class="block font-bold text-gray-700 mb-2">Select Module</label>
                <select name="module_id"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 outline-none transition"
                    required>
                    @foreach($modules as $module)
                        <option value="{{ $module->id }}" {{ $lesson->module_id == $module->id ? 'selected' : '' }}>
                            {{ $module->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Lesson Title -->
            <div class="mb-5">
                <label class="block font-bold text-gray-700 mb-2">Lesson Title</label>
                <input type="text" name="title" value="{{ $lesson->title }}"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 outline-none transition"
                    required>
            </div>

            <!-- Content Type -->
            <div class="mb-5">
                <label class="block font-bold text-gray-700 mb-2">Content Type</label>
                <select name="content_type"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 outline-none transition"
                    required>
                    <option value="text" {{ $lesson->content_type === 'text' ? 'selected' : '' }}>Text / Article</option>
                    <option value="video" {{ $lesson->content_type === 'video' ? 'selected' : '' }}>YouTube Video Embed Link
                    </option>
                </select>
            </div>

            <!-- Lesson Content -->
            <div class="mb-6">
                <label class="block font-bold text-gray-700 mb-2">Content</label>
                <textarea name="content"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 outline-none transition"
                    rows="6" required>{{ $lesson->content }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-700 transition shadow-sm">
                    Update Lesson
                </button>
            </div>
        </form>
    </div>
@endsection