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

                <div class="flex flex-col sm:flex-row items-center gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Profile Photo</label>
                        @if(auth()->user()->image)
                            <img src="{{ asset('storage/' . auth()->user()->image) }}"
                                class="h-24 w-24 rounded-full object-cover my-3 border-2 border-indigo-100" alt="Current Photo">
                        @else
                            <div class="h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center my-3 text-gray-400">
                                No Image
                            </div>
                        @endif

                        <input type="file" name="image" accept="image/*"
                            class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 w-full">
                        <p class="text-xs text-gray-400 mt-2">JPG, JPEG or PNG. Max size of 5MB.</p>
                        @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection