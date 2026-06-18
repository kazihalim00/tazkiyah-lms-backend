@extends('layouts.app')

@section('title', 'Admin - Course Management')

@section('content')
    <div class="max-w-6xl mx-auto space-y-8">

        <!-- Admin Control Header -->
        <div
            class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-slate-900 text-white p-6 md:p-8 rounded-3xl shadow-xl border border-slate-800">
            <div class="space-y-1">
                <span
                    class="bg-indigo-500 text-white text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">
                    Control Center
                </span>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight">LMS Admin Dashboard</h1>
                <p class="text-slate-400 text-xs font-medium">Manage your spiritual development courses, structured modules,
                    and lessons.</p>
            </div>

            <!-- Action Trigger Buttons Zone -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.courses.create') }}"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs px-5 py-3 rounded-xl transition shadow-md flex items-center gap-2">
                    ➕ Create Course
                </a>
                <a href="{{ route('admin.modules.create') }}"
                    class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs px-4 py-3 rounded-xl transition">
                    📦 Add Module
                </a>
                <a href="{{ route('admin.lessons.create') }}"
                    class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs px-4 py-3 rounded-xl transition">
                    📝 Add Lesson
                </a>
            </div>
        </div>

        <!-- Overview Stats Tracker -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-gray-100 p-5 rounded-2xl shadow-sm">
                <p class="text-xs font-bold text-gray-400 uppercase">Total System Courses</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ $courses->count() }}</p>
            </div>
            <div class="bg-white border border-gray-100 p-5 rounded-2xl shadow-sm">
                <p class="text-xs font-bold text-gray-400 uppercase">Configured Modules</p>
                <p class="text-2xl font-black text-indigo-600 mt-1">{{ \App\Models\Module::count() ?? 0 }}</p>
            </div>
            <div class="bg-white border border-gray-100 p-5 rounded-2xl shadow-sm col-span-2 sm:col-span-1">
                <p class="text-xs font-bold text-gray-400 uppercase">Published Lessons</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ \App\Models\Lesson::count() ?? 0 }}</p>
            </div>
        </div>

        <!-- Course Datatable List -->
        <div class="bg-white border border-gray-100 rounded-3xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                <h2 class="text-lg font-extrabold text-gray-900">Active Course Repositories</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-wider border-b border-gray-100">
                            <th class="py-4 px-6">Course Identity & Cover</th>
                            <th class="py-4 px-6">Structured Architecture</th>
                            <th class="py-4 px-6 text-right">Administrative Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm font-medium text-gray-600">
                        @forelse($courses as $course)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-4 px-6 flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-xl shrink-0">
                                        📚
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-950 tracking-tight">{{ $course->title }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5 max-w-xs truncate">
                                            {{ $course->description ?? 'No specific module details.' }}
                                        </p>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex gap-3 text-xs">
                                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-md font-bold">
                                            {{ $course->modules->count() ?? 0 }} Modules
                                        </span>
                                        <span class="bg-indigo-50 text-indigo-600 px-2 py-1 rounded-md font-bold">
                                            {{ $course->lessons?->count() ?? 0 }} Total Lessons
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.courses.edit', $course->id) }}"
                                            class="text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this course?')"
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
                                    📭 No courses have been registered in the system database yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection