@extends('layouts.app')

@section('title', 'Manage Lessons')
@section('header_title', 'Lessons Management')

@section('content')
    <div class="max-w-6xl mx-auto space-y-8 mt-4">

        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800 tracking-tight">Manage Lessons</h2>
            <a href="{{ route('admin.lessons.create') }}"
                class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-indigo-700 transition shadow-sm flex items-center gap-2 w-fit">
                ➕ Add New Lesson
            </a>
        </div>

        @if(session('success'))
            <div
                class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-xl mb-6 font-semibold shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-wider border-b border-gray-100">
                            <th class="py-4 px-6">Lesson Title</th>
                            <th class="py-4 px-6">Module Assigned</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm font-medium text-gray-600">
                        @forelse($lessons as $lesson)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-4 px-6 font-bold text-gray-900">{{ $lesson->title }}</td>
                                <td class="py-4 px-6">
                                    <span class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-md font-bold text-xs">
                                        {{ $lesson->module->title ?? 'Unassigned' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.lessons.edit', $lesson->id) }}"
                                            class="text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition">
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.lessons.destroy', $lesson->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this lesson?');"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-12 text-center text-gray-400 font-bold">
                                    📭 No lessons found. Add one to get started!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection