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
                <div class="mt-8 flex justify-between items-center border-t border-gray-100 pt-6">

                    @php

                        $isCompleted = \App\Models\LessonCompletion::where('user_id', auth()->id())
                            ->where('lesson_id', $lesson->id)
                            ->exists();
                    @endphp

                    @if($isCompleted)
                        <button disabled
                            class="bg-gray-100 text-emerald-600 border-2 border-emerald-500 px-8 py-3 rounded-xl font-bold cursor-not-allowed shadow-sm flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Completed
                        </button>
                    @else
                        <form action="{{ route('lesson.complete', $lesson->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-emerald-700 transition shadow-sm">
                                Complete Lesson
                            </button>
                        </form>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection