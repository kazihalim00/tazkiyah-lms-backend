@extends('layouts.app')
@section('title', 'Manage Modules')

@section('content')
    <div class="max-w-6xl mx-auto mt-10">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Manage Modules</h2>
            <a href="{{ route('admin.modules.create') }}"
                class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-indigo-700 transition shadow-sm">
                + Add New Module
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
                        <th class="p-4 font-semibold">Module Title</th>
                        <th class="p-4 font-semibold">Course Name</th>
                        <th class="p-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($modules as $module)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                            <td class="p-4 font-medium text-gray-800">{{ $module->title }}</td>
                            <td class="p-4 text-gray-600">{{ $module->course->title ?? 'N/A' }}</td>
                            <td class="p-4 text-right flex justify-end gap-3">
                                <span class="text-gray-400 text-sm italic">Edit/Delete coming soon</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-8 text-center text-gray-500">No modules found. Add one to get started!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection