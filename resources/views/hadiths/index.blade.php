@extends('layouts.app')

@section('title', 'Hadith Collection')
@section('header_title', 'Al-Hadith')

@section('content')
    <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        <div class="mb-10 text-center">
            <span
                class="bg-emerald-100 text-emerald-700 px-4 py-1.5 rounded-full text-xs font-black tracking-widest uppercase mb-4 inline-block border border-emerald-200">
                Sacred Texts
            </span>
            <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight mb-3">Hadith Collection</h1>
            <p class="text-gray-500 text-base font-medium">Read, learn, and earn points from the authentic sayings.</p>
        </div>

        <div class="flex flex-col gap-4">
            @forelse($categories as $category)
                <a href="{{ route('hadiths.category', $category->slug) }}"
                    class="group relative bg-white rounded-2xl p-5 sm:p-6 border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 hover:border-emerald-200 transition-all duration-300 flex flex-col sm:flex-row sm:items-center justify-between gap-4 overflow-hidden">

                    <div
                        class="absolute left-0 top-0 w-1.5 h-full bg-emerald-500 scale-y-0 group-hover:scale-y-100 transition-transform origin-center duration-300">
                    </div>

                    <div class="flex items-center gap-5 relative z-10 pl-2">
                        <div
                            class="w-12 h-12 shrink-0 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        </div>

                        <div>
                            <h3 class="text-xl font-bold text-gray-800 group-hover:text-emerald-700 transition-colors">
                                {{ $category->name_bn }}
                            </h3>
                            @if($category->name_en && strtolower($category->name_bn) !== strtolower($category->name_en))
                                <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-wider">
                                    {{ $category->name_en }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between sm:justify-end gap-6 relative z-10 pl-2 sm:pl-0">
                        <span
                            class="bg-gray-50 text-gray-600 group-hover:bg-emerald-50 group-hover:text-emerald-700 px-4 py-1.5 rounded-full text-sm font-bold border border-gray-100 group-hover:border-emerald-200 transition-colors whitespace-nowrap">
                            {{ $category->hadiths_count }} Hadiths
                        </span>

                        <div
                            class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-emerald-600 transform group-hover:translate-x-1 transition-all"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            @empty
                <div class="py-16 bg-white rounded-3xl border border-gray-100 text-center shadow-sm">
                    <h3 class="text-xl font-bold text-gray-800">No Categories Found</h3>
                </div>
            @endforelse
        </div>
    </div>
@endsection