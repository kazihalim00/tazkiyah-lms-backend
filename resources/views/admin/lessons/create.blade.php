@extends('layouts.app')
@section('title', 'Add New Lesson')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-100 mt-10">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Add New Lesson</h2>

        @if(session('success'))
            <div class="bg-emerald-100 text-emerald-800 p-4 rounded-lg mb-6 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.lessons.store') }}" method="POST">
            @csrf

            <div class="mb-5">
                <label class="block font-bold text-gray-700 mb-2">Select Module</label>
                <select name="module_id"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                    required>
                    <option value="" disabled selected>-- Choose a Module --</option>
                    @foreach($modules as $module)
                        <option value="{{ $module->id }}">{{ $module->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-5">
                <label class="block font-bold text-gray-700 mb-2">Lesson Title</label>
                <input type="text" name="title"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                    placeholder="e.g. Introduction to the topic" required>
            </div>

            <div class="mb-5">
                <label class="block font-bold text-gray-700 mb-2">Content Type</label>
                <select name="content_type"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                    required>
                    <option value="text">Text / Article</option>
                    <option value="video">YouTube Video Embed Link</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block font-bold text-gray-700 mb-2">Content</label>
                <textarea name="content"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                    rows="6" placeholder="Write your lesson content or paste video embed URL here..." required></textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-700 transition shadow-sm">
                    Save Lesson
                </button>
            </div>
        </form>
    </div>
@endsection