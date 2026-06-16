@extends('layouts.app')

@section('title', 'Edit Profile - Tazkiyah')
@section('header_title', 'Edit Profile')

@section('content')
    <div class="max-w-3xl mx-auto">

        @if(session('success'))
            <div
                class="bg-emerald-50 text-emerald-600 p-4 rounded-xl mb-6 text-sm font-medium border border-emerald-100 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Profile Image Section -->
                <div class="flex flex-col sm:flex-row items-center gap-6 mb-8">
                    <div>
                        @if(auth()->user()->image)
                            <img src="{{ asset('storage/' . auth()->user()->image) }}" alt="Profile"
                                class="h-24 w-24 rounded-full object-cover border-4 border-indigo-50 shadow-md">
                        @else
                            <div
                                class="h-24 w-24 bg-indigo-600 rounded-full flex items-center justify-center text-3xl font-bold text-white border-4 border-indigo-50 shadow-md uppercase">
                                {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="text-center sm:text-left">
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Profile Photo</h3>
                        <input type="file" name="image" accept="image/*"
                            class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 w-full">
                        <p class="text-xs text-gray-400 mt-2">JPG, JPEG or PNG. Max size of 2MB.</p>
                        @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <hr class="border-gray-100 mb-8">

                <!-- Personal Info Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-medium hover:bg-indigo-700 transition shadow-sm w-full sm:w-auto">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection