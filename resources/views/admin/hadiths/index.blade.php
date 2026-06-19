@extends('layouts.app')

@section('title', 'Manage Hadiths')
@section('header_title', 'Admin Panel - Hadith Collection')

@section('content')
    <div class="max-w-6xl mx-auto py-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Manage Hadiths</h1>
                <p class="text-sm text-gray-500 mt-1">View and manage all uploaded hadiths</p>
            </div>
            <a href="{{ route('admin.hadiths.create') }}"
                class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-indigo-700 transition shadow-sm">
                + Add New Hadith
            </a>
        </div>

        @if(session('success'))
            <div
                class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl shadow-sm mb-6 font-bold flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-black border-b border-gray-100">Reference / Grade</th>
                            <th class="p-4 font-black border-b border-gray-100">Category & Chapter</th>
                            <th class="p-4 font-black border-b border-gray-100">Arabic / Bangla Snippet</th>
                            <th class="p-4 font-black border-b border-gray-100">Points</th>
                            <th class="p-4 font-black border-b border-gray-100 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($hadiths as $hadith)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="p-4 align-top">
                                    <span class="font-extrabold text-indigo-700 block">{{ $hadith->reference }}</span>
                                    <span
                                        class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded mt-1 inline-block">{{ $hadith->grade }}</span>
                                </td>
                                <td class="p-4 align-top">
                                    <span class="font-bold text-gray-800 block">{{ $hadith->category->name_bn ?? 'N/A' }}</span>
                                    @if($hadith->subCategory)
                                        <span class="text-xs text-gray-500 font-bold block mt-0.5 truncate max-w-[200px]"
                                            title="{{ $hadith->subCategory->name_bn }}">
                                            ↳ {{ $hadith->subCategory->name_bn }}
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 align-top max-w-[300px]">
                                    <p class="text-gray-900 font-bold text-sm truncate" dir="rtl">{{ $hadith->arabic_text }}</p>
                                    <p class="text-gray-500 text-xs mt-1 truncate">{{ $hadith->bangla_text }}</p>
                                </td>
                                <td class="p-4 align-top text-gray-600 font-bold">
                                    {{ $hadith->points }}
                                </td>
                                <td class="p-4 align-top text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <<a href="{{ route('admin.hadiths.edit', $hadith->id) }}"
                                            class="text-indigo-500 hover:text-indigo-700 transition p-1 bg-indigo-50 hover:bg-indigo-100 rounded"
                                            title="Edit Hadith">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400 font-bold">
                                    No hadiths uploaded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection