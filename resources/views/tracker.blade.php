@extends('layouts.app')
@section('title', 'Ibadah Tracker')
@section('header_title', 'Daily Tracker')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl mb-6 font-medium border border-emerald-200">
                {{ session('success') }}
            </div>
        @endif
        <form action="{{ url('/tracker') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <!-- Date -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Date</label>
                    <input type="date" name="date" required class="w-full px-4 py-3 rounded-xl border border-gray-300">
                </div>

                <!-- Fajr Selection -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Fajr Prayer</label>
                    <div class="grid grid-cols-3 gap-4">
                        <label class="border p-4 rounded-xl cursor-pointer hover:bg-indigo-50"><input type="radio"
                                name="fajr" value="Jamaah" class="mr-2"> Jamaah</label>
                        <label class="border p-4 rounded-xl cursor-pointer hover:bg-indigo-50">
                            <input type="radio" name="fajr" value="Individual" class="mr-2"> Individual
                        </label>
                        <label class="border p-4 rounded-xl cursor-pointer hover:bg-indigo-50"><input type="radio"
                                name="fajr" value="Missed" class="mr-2"> Missed</label>
                    </div>
                </div>

                <!-- Adhkar -->
                <div class="flex items-center gap-4">
                    <input type="checkbox" name="morning_adhkar" value="1" class="w-6 h-6 text-indigo-600 rounded">
                    <label class="text-gray-700 font-bold">Morning Adhkar Completed</label>
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 text-white font-bold py-4 rounded-xl hover:bg-indigo-700">Save
                    Tracker</button>
            </div>
        </form>
    </div>
@endsection