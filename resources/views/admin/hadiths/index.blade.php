@extends('layouts.app')

@section('title', 'Hadith Corner')
@section('header_title', 'Hadith Corner')

@section('content')
    <div class="max-w-6xl mx-auto py-8">
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">Hadith Collection</h1>
            <p class="text-gray-500 text-lg">Gain knowledge from authentic sources and earn points.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($categories as $category)
                <a href="{{ route('hadiths.category', $category->slug) }}" class="group block">
                    <div
                        class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg hover:border-indigo-100 transition-all duration-300 transform hover:-translate-y-1">
                        <div
                            class="h-14 w-14 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $category->name_bn }}</h3>
                        <p class="text-sm text-gray-400 font-bold uppercase tracking-wide mb-4">{{ $category->name_en }}</p>
                        <div class="inline-block bg-gray-50 text-gray-600 text-xs font-bold px-3 py-1.5 rounded-lg">
                            {{ $category->hadiths_count }} Hadiths
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-400 font-bold">No collections available yet. Admin is working on it!</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection