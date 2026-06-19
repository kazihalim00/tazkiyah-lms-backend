@extends('layouts.app')

@section('title', $category->name_bn . ' - Hadiths')
@section('header_title', $category->name_bn)

@section('content')
    <div class="max-w-4xl mx-auto py-8 space-y-8">

        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('hadiths.index') }}" class="text-gray-400 hover:text-indigo-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900">{{ $category->name_bn }} <span
                        class="text-gray-400 text-lg font-medium">({{ $category->name_en }})</span></h1>
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
        @if(session('info'))
            <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded-xl shadow-sm mb-6 font-bold">
                {{ session('info') }}
            </div>
        @endif

        @foreach($uncategorizedHadiths as $hadith)
            @include('partials.hadith_card', ['hadith' => $hadith])
        @endforeach

        @foreach($subCategories as $subCategory)
            @if($subCategory->hadiths->count() > 0)
                <div
                    class="mt-12 mb-6 border-l-4 border-emerald-500 pl-4 bg-emerald-50/50 p-3 rounded-r-xl flex items-center gap-3 shadow-sm">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                    <h2 class="text-xl font-bold text-gray-900">{{ $subCategory->name_bn }} <span
                            class="text-gray-500 text-sm font-medium ml-2">{{ $subCategory->name_en ? '(' . $subCategory->name_en . ')' : '' }}</span>
                    </h2>
                </div>

                <div class="space-y-8 pl-0 md:pl-6 border-l border-gray-100/50">
                    @foreach($subCategory->hadiths as $hadith)
                        @include('partials.hadith_card', ['hadith' => $hadith])
                    @endforeach
                </div>
            @endif
        @endforeach

        @if($uncategorizedHadiths->isEmpty() && $subCategories->isEmpty())
            <div class="bg-white p-12 rounded-3xl text-center border border-gray-100">
                <div class="h-16 w-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                </div>
                <p class="text-gray-500 font-bold text-lg">No hadiths available in this category yet.</p>
            </div>
        @endif
    </div>
@endsection