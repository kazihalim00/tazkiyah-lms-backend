@extends('layouts.app')
@section('title', 'Edit Module')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-100 mt-10">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Edit Module</h2>
            <a href="{{ route('admin.modules.index') }}" class="text-indigo-600 font-bold hover:underline">← Back</a>
        </div>

        <form action="{{ route('admin.modules.update', $module->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-5">
                <label class="block font-bold text-gray-700 mb-2">Select Course</label>
                <select name="course_id"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 outline-none transition"
                    required>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $module->course_id == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block font-bold text-gray-700 mb-2">Module Title</label>
                <input type="text" name="title" value="{{ $module->title }}"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 outline-none transition"
                    required>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-700 transition shadow-sm">
                    Update Module
                </button>
            </div>
        </form>
    </div>
@endsection