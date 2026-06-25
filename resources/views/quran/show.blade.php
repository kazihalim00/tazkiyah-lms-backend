@extends('layouts.app')

@section('title', $surah->name_bn)

@section('content')
    <div class="max-w-3xl mx-auto py-8">
        <h1 class="text-center text-3xl font-black mb-10">{{ $surah->name_bn }}</h1>

        @foreach($surah->ayahs as $ayah)
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-6">
                <p class="text-right text-3xl font-serif text-gray-800 leading-loose mb-6">{{ $ayah->arabic_text }}</p>

                <p class="text-gray-600 font-medium mb-6">{{ $ayah->bangla_text }}</p>

                <form action="{{ route('quran.tadabbur.save', $ayah->id) }}" method="POST" class="mt-4 border-t pt-4">
                    @csrf
                    <select name="reference"
                        class="w-full p-2 mb-3 bg-gray-50 rounded-lg text-sm border-none focus:ring-2 focus:ring-indigo-500 font-bold text-gray-700">
                        <option value="Personal Reflection">Personal Reflection</option>
                        <option value="Tafsir Ibn Kathir">Tafsir Ibn Kathir</option>
                        <option value="Tafsir As-Sa'di">Tafsir As-Sa'di</option>
                        <option value="Tafsir Al-Baghawi">Tafsir Al-Baghawi</option>
                    </select>

                    <textarea name="tadabbur_note"
                        class="w-full p-3 bg-gray-50 rounded-xl text-sm border-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Write your Tadabbur or reflection on this verse..."></textarea>

                    <div class="flex justify-end mt-2">
                        <button type="submit"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-indigo-700 transition">
                            Save Tadabbur & Earn Points
                        </button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
@endsection