@extends('layouts.app')
@section('title', $lesson->title)
@section('header_title', 'Lesson')

@section('content')
    <div class="max-w-5xl mx-auto">
        <a href="{{ url()->previous() }}" class="text-indigo-600 font-bold mb-6 inline-block hover:underline">
            ← Back to Modules
        </a>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">{{ $lesson->title }}</h1>

            @if($lesson->content_type === 'video')
                <div class="aspect-video w-full mb-8 overflow-hidden rounded-2xl bg-gray-900">
                    <iframe class="w-full h-full" src="{{ $lesson->content }}" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>
            @else
                <div class="prose prose-indigo max-w-none text-gray-700 leading-relaxed">
                    {!! $lesson->content !!}
                </div>
            @endif
        </div>

        <div class="mt-8 flex justify-between items-center">
            <button class="text-gray-500 font-bold hover:text-gray-800 transition">← Previous</button>

            <form action="{{ route('lesson.complete', $lesson->id) }}" method="POST">
                @csrf
                <button type="submit"
                    class="bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-emerald-700 transition shadow-sm">
                    Complete Lesson
                </button>
            </form>
        </div>
    </div>
@endsection