@extends('layouts.app')
@section('title', 'Manage Lessons')

@section('content')
    <div class="max-w-6xl mx-auto mt-10">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Manage Lessons</h2>
            <a href="{{ route('admin.lessons.create') }}"
                class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-indigo-700 transition">
                + Add New Lesson
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 text-emerald-800 p-4 rounded-lg mb-6 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-600">
                        <th class="p-4 font-semibold">Title</th>
                        <th class="p-4 font-semibold">Module</th>
                        <th class="p-4 font-semibold">Type</th>
                        <th class="p-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lessons as $lesson)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                            <td class="p-4 font-medium text-gray-800">{{ $lesson->title }}</td>
                            <td class="p-4 text-gray-600">{{ $lesson->module->title ?? 'N/A' }}</td>
                            <td class="p-4 text-gray-600 capitalize">{{ $lesson->content_type }}</td>
                            <td class="p-4 text-right flex justify-end gap-3">
                                <a href="{{ route('admin.lessons.edit', $lesson->id) }}"
                                    class="text-blue-500 font-bold hover:underline">Edit</a>
                                <form action="{{ route('admin.lessons.destroy', $lesson->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this lesson?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 font-bold hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-500">No lessons found. Add one to get started!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection