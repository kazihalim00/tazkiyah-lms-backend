@extends('layouts.app')

@section('title', $subCategory->name_bn)
@section('header_title', 'Chapter Hadiths')

@section('content')
    <div class="max-w-4xl mx-auto py-8 space-y-8">

        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('hadiths.category', $subCategory->category->slug) }}"
                class="text-gray-400 hover:text-indigo-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900">{{ $subCategory->name_bn }}</h1>
                <p class="text-sm font-bold text-gray-400 mt-1 uppercase">{{ $subCategory->category->name_bn }}</p>
            </div>
        </div>

        @if(session('success'))
            <div
                class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl shadow-sm mb-6 font-bold flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-8">
            @forelse($hadiths as $hadith)
                @include('partials.hadith_card', ['hadith' => $hadith])
            @empty
                <div class="text-center py-12 bg-white rounded-3xl border border-gray-100">
                    <p class="text-gray-400 font-bold">No hadiths found in this chapter.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection