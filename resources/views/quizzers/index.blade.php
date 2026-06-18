@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto py-10">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">All Quizzes</h1>
            <a href="{{ route('admin.quizzes.create') }}"
                class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-indigo-700">
                + Create New Quiz
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-6">{{ session('success') }}</div>
        @endif

        <div class="bg-white shadow rounded-2xl overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-4">Title</th>
                        <th class="p-4">Total Questions</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quizzes as $quiz)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4 font-medium">{{ $quiz->title }}</td>
                            <td class="p-4">{{ $quiz->questions->count() }}</td>
                            <td class="p-4 text-right flex justify-end gap-2">
                                <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 font-bold hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-8 text-center text-gray-500">No quizzes created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection