@extends('layouts.app')

@section('title', 'Advanced Ibadah Tracker')

@section('content')
    <div class="max-w-5xl mx-auto" x-data="{
                        fajr: '{{ $tracker->fajr ?? 'missed' }}',
                        dhuhr: '{{ $tracker->dhuhr ?? 'missed' }}',
                        asr: '{{ $tracker->asr ?? 'missed' }}',
                        maghrib: '{{ $tracker->maghrib ?? 'missed' }}',
                        isha: '{{ $tracker->isha ?? 'missed' }}',
                        khushu: {{ $tracker->khushu_level ?? 5 }}
                    }">

        <!-- Professional Header with Date -->
        <div
            class="flex flex-col md:flex-row justify-between items-center mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">Today's Journey</h1>
                <p class="text-gray-500">Track your worship, build accountability, and grow your Imaan.</p>
            </div>
            <div class="bg-indigo-50 text-indigo-700 px-6 py-3 rounded-xl font-bold border border-indigo-100">
                {{ \Carbon\Carbon::now()->format('l, j F Y') }}
            </div>
        </div>

        <!-- Accountability Partner Section -->
        <div
            class="mb-8 p-6 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl text-white shadow-lg flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold">Accountability Partner</h3>
                <p class="text-indigo-100 text-sm">Your current level: {{ auth()->user()->level ?? 'Beginner' }}</p>
            </div>

            <a href="{{ route('community.index') }}"
                class="bg-white/20 hover:bg-white/30 px-5 py-2.5 rounded-lg font-bold transition text-sm">
                Find Partner
            </a>
        </div>

        <form action="{{ url('/tracker') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Fardh Salah Section -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold mb-6">Fardh Salah (Obligatory)</h2>
                @foreach(['Fajr', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'] as $p)
                    @php $m = strtolower($p); @endphp
                    <div
                        class="flex flex-col md:flex-row items-center justify-between py-4 border-b border-gray-50 last:border-0">
                        <span class="font-bold text-gray-800 w-24">{{ $p }}</span>
                        <input type="hidden" name="{{ $m }}" x-model="{{ $m }}">
                        <div class="flex flex-wrap gap-2">
                            @foreach(['jamaah_mosque' => 'Mosque', 'jamaah_home' => 'Home', 'alone' => 'Alone', 'qada' => 'Qada', 'missed' => 'Missed'] as $val => $label)
                                <button type="button" @click="{{ $m }} = '{{ $val }}'" :class="{
                                                                                        'bg-emerald-600 text-white shadow-md': {{ $m }} === '{{ $val }}' && '{{ $val }}' === 'jamaah_mosque',
                                                                                        'bg-teal-500 text-white shadow-md': {{ $m }} === '{{ $val }}' && '{{ $val }}' === 'jamaah_home',
                                                                                        'bg-blue-500 text-white shadow-md': {{ $m }} === '{{ $val }}' && '{{ $val }}' === 'alone',
                                                                                        'bg-orange-500 text-white shadow-md': {{ $m }} === '{{ $val }}' && '{{ $val }}' === 'qada',
                                                                                        'bg-red-500 text-white shadow-md': {{ $m }} === '{{ $val }}' && '{{ $val }}' === 'missed',
                                                                                        'bg-gray-100 text-gray-600': {{ $m }} !== '{{ $val }}'
                                                                                    }"
                                    class="px-4 py-2 text-xs font-bold rounded-lg transition-all">{{ $label }}</button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Balanced Grid for Sunnah/Focus -->
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Sunnah Section -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col">
                    <h3 class="font-bold mb-6">Sunnah, Adhkar & Sadaqah</h3>
                    <div class="grid grid-cols-1 gap-3">
                        @foreach(['morning_adhkar' => 'Morning Adhkar', 'evening_adhkar' => 'Evening Adhkar', 'tahajjud' => 'Tahajjud', 'witr' => 'Witr', 'sadaqah' => 'Sadaqah (Charity)', 'duwa' => 'Daily Duwa'] as $key => $label)
                            <label
                                class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl cursor-pointer hover:bg-indigo-50 transition border border-transparent hover:border-indigo-100">
                                <input type="checkbox" name="{{ $key }}" value="1" {{ (isset($tracker) && $tracker->$key) ? 'checked' : '' }} class="w-5 h-5 rounded text-indigo-600 border-gray-300">
                                <span class="text-sm font-bold text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Focus & Quran Section (Updated Font & Spacing) -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <h3 class="font-bold text-xl mb-8">Focus & Quran</h3>

                    <div class="mb-10">
                        <label class="block text-base font-bold text-gray-800 mb-5">
                            Khushu Level: <span x-text="khushu" class="text-indigo-600 text-2xl"></span>/10
                        </label>
                        <input type="range" name="khushu_level" x-model="khushu" min="1" max="10"
                            class="w-full h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                    </div>

                    <div>
                        <label class="block text-base font-bold text-gray-800 mb-4">Quran Recitation (Pages)</label>
                        <input type="number" name="quran_pages" value="{{ $tracker->quran_pages ?? 0 }}"
                            class="w-full p-5 bg-gray-50 rounded-xl border border-gray-200 text-lg font-semibold focus:border-indigo-500 outline-none">
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-gray-900 text-white py-5 rounded-2xl font-bold hover:bg-indigo-600 transition shadow-xl text-lg">
                Save Today's Progress
            </button>
        </form>
    </div>
@endsection