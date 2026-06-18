@extends('layouts.app')

@section('title', 'Edit Quiz - Admin')
@section('header_title', 'Edit Quiz')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Edit Quiz Details</h2>
            <a href="{{ route('admin.quizzes.index') }}" class="text-indigo-600 font-bold hover:underline">
                &larr; Back to Quizzes
            </a>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <form action="{{ route('admin.quizzes.update', $quiz->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Quiz Title</label>
                        <input type="text" name="title" value="{{ old('title', $quiz->title) }}" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="4" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800">{{ old('description', $quiz->description) }}</textarea>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-700 transition shadow-sm">
                            Update Quiz
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection