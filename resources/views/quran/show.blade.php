@extends('layouts.app')

@section('title', $surah->name_bangla)

@section('content')
    <div class="max-w-3xl mx-auto py-8 px-4">

        {{-- Back Button & Title Header --}}
        <div class="flex items-center mb-10 relative">
            <a href="{{ route('quran.index') }}"
                class="absolute left-0 w-10 h-10 rounded-full bg-white flex items-center justify-center text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition shadow-sm border border-gray-100 z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-black text-gray-900 w-full text-center">{{ $surah->name_bangla }}</h1>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl shadow-sm mb-6 font-bold">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error Message --}}
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl shadow-sm mb-6 font-bold">
                {{ session('error') }}
            </div>
        @endif

        @foreach($surah->ayahs as $ayah)
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm mb-6 relative">

                {{-- Arabic Text --}}
                <p class="text-right text-3xl md:text-4xl font-serif text-gray-800 leading-loose mb-6" dir="rtl"
                    style="line-height: 2.2;">
                    {{ $ayah->arabic_text }}
                </p>

                {{-- Bangla Translation --}}
                <p class="text-gray-600 font-medium mb-6 text-lg leading-relaxed">{{ $ayah->bangla_text }}</p>

                {{-- Tafsir --}}
                @if($ayah->tafsir)
                    <div class="mb-6 p-4 bg-amber-50 rounded-2xl border border-amber-100/50">
                        <h4 class="font-bold text-amber-800 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            তাফসীর (Tafsir)
                        </h4>
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                            {{ $ayah->tafsir }}
                        </p>
                    </div>
                @endif

                {{-- Actions: Tadabbur Form & Point Button --}}
                <div class="border-t border-gray-50 pt-5 mt-2 flex flex-col md:flex-row gap-4 items-end">

                    {{-- Tadabbur Form --}}
                    <form action="{{ route('quran.tadabbur.save', $ayah->id) }}" method="POST" class="flex-grow w-full">
                        @csrf
                        <textarea name="tadabbur_note" rows="1"
                            class="w-full p-3 bg-gray-50 rounded-xl text-sm border border-gray-100 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition resize-none placeholder-gray-400"
                            placeholder="Write your Tadabbur for this Ayah..." required></textarea>
                        <div class="mt-2 text-right">
                            <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-indigo-700 transition shadow-sm">
                                Save Tadabbur
                            </button>
                        </div>
                    </form>

                    {{-- Point Button --}}
                    <form action="{{ route('quran.ayah.read', $ayah->id) }}" method="POST" class="w-full md:w-auto">
                        @csrf
                        @if(session()->has('read_ayah_' . $ayah->id . '_' . auth()->id()))
                            <button type="button" disabled
                                class="w-full md:w-auto bg-gray-50 text-emerald-600 px-4 py-3 rounded-xl text-xs font-bold flex items-center justify-center gap-2 cursor-not-allowed border border-emerald-100">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                5 Pts Added
                            </button>
                        @else
                            <button type="submit"
                                class="w-full md:w-auto bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-4 py-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 group">
                                <svg class="w-4 h-4 opacity-70 group-hover:rotate-12 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Read (+5 Pts)
                            </button>
                        @endif
                    </form>
                </div>

            </div>
        @endforeach
    </div>
@endsection