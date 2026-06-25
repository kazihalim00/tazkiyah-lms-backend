@extends('layouts.app')

@section('title', 'Al-Quran')
@section('header_title', 'Al-Quran')

@section('content')
    <div class="max-w-4xl mx-auto py-8">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-black text-gray-900">আল-কুরআনুল কারীম</h1>
            <p class="text-gray-500 font-bold mt-2">তাদাব্বুর করুন এবং ইলম অর্জন করুন</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($surahs as $surah)
                <a href="{{ route('quran.show', $surah->id) }}"
                    class="bg-white p-5 rounded-2xl border border-gray-100 hover:border-indigo-200 hover:shadow-md transition flex items-center justify-between group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-10 h-10 flex items-center justify-center bg-indigo-50 text-indigo-700 font-bold rounded-lg group-hover:bg-indigo-600 group-hover:text-white transition">
                            {{ $surah->surah_no }}
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">{{ $surah->name_bn }}</h3>
                            <p class="text-xs text-gray-400 font-bold uppercase">{{ $surah->name_en }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-600">{{ $surah->ayahs_count }} আয়াত</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection