@extends('layouts.app')

@section('title', $surah->name_bangla)

@section('content')
    <div class="max-w-3xl mx-auto py-8">
        <h1 class="text-center text-3xl font-black mb-10">{{ $surah->name_bangla }}</h1>

        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl shadow-sm mb-6 font-bold">
                {{ session('success') }}
            </div>
        @endif

        @foreach($surah->ayahs as $ayah)
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-6">
                <p class="text-right text-3xl font-serif text-gray-800 leading-loose mb-6">{{ $ayah->arabic_text }}</p>

                <p class="text-gray-600 font-medium mb-6">{{ $ayah->bangla_text }}</p>
                <div class="mb-6 p-4 bg-amber-50 rounded-xl border border-amber-100">
                    <h4 class="font-bold text-amber-800 mb-2">Tafsir</h4>
                    <p class="text-sm text-gray-700 leading-relaxed">
                        {{ $ayah->tafsir }}
                    </p>
                </div>

                <form action="{{ route('quran.tadabbur.save', $ayah->id) }}" method="POST" class="border-t pt-4">
                    @csrf
                    <textarea name="tadabbur_note"
                        class="w-full p-3 bg-gray-50 rounded-xl text-sm border-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Write your Tadabbur..." required></textarea>

                    <div class="flex justify-end mt-2">
                        <button type="submit"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-indigo-700 transition">
                            Save Tadabbur
                        </button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
@endsection