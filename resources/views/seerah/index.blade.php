@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Ultimate Seerah Masterclass</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($classes as $class)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                    <div class="h-48 bg-indigo-50 flex items-center justify-center">
                        <svg class="w-12 h-12 text-indigo-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z" />
                        </svg>
                    </div>

                    <div class="p-5">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">{{ $class->title }}</h3>

                        <div class="flex gap-2">
                            <a href="{{ $class->video_url }}" target="_blank"
                                class="flex-1 bg-indigo-600 text-white text-center py-2 rounded-lg font-semibold hover:bg-indigo-700 transition text-sm">
                                Watch Class
                            </a>

                            @if($class->doc_url)
                                <a href="{{ $class->doc_url }}" target="_blank"
                                    class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg font-semibold hover:bg-gray-200 transition text-sm">
                                    Resources
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection