@extends('layouts.app')
@section('title', 'Manage Courses')

@section('content')
    <div class="max-w-6xl mx-auto mt-10">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Manage Courses</h2>
            <a href="{{ route('admin.courses.create') }}"
                class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-indigo-700 transition shadow-sm">
                + Add New Course
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
                        <th class="p-4 font-semibold">Description</th>
                        <th class="p-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                            <td class="p-4 font-medium text-gray-800">{{ $course->title }}</td>
                            <td class="p-4 text-gray-600 truncate max-w-xs">{{ $course->description ?? 'No description added' }}
                            </td>
                            <td class="p-4 text-right flex justify-end gap-3">
                                <span class="text-gray-400 text-sm italic">Edit/Delete coming soon</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-8 text-center text-gray-500">No courses found. Add one to get started!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection