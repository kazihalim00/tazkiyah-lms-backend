@extends('layouts.app')

@section('title', $quiz->title)
@section('header_title', 'Quiz Assessment')

@section('content')
    <div class="max-w-3xl mx-auto py-8">

        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl shadow-sm mb-6 font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-8">
            <h1 class="text-2xl font-black text-gray-800 mb-2">{{ $quiz->title }}</h1>
            <p class="text-gray-600 text-sm leading-relaxed">{{ $quiz->description }}</p>
        </div>

        <form action="{{ route('student.quizzes.submit', $quiz->id) }}" method="POST">
            @csrf

            <div class="space-y-6">
                @foreach($quiz->questions as $index => $question)
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            <span class="text-indigo-600 mr-1">{{ $loop->iteration }}.</span> {{ $question->question_text }}
                        </h3>

                        <div class="space-y-3 pl-5">
                            @foreach($question->options as $option)
                                <label
                                    class="flex items-center gap-3 cursor-pointer p-3 rounded-xl hover:bg-gray-50 border border-transparent hover:border-gray-100 transition">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" required
                                        class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 focus:ring-indigo-500">
                                    <span class="text-gray-700 font-medium">{{ $option->option_text }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="bg-indigo-600 text-white px-10 py-3.5 rounded-xl font-black hover:bg-indigo-700 transition shadow-md text-lg">
                    Submit Quiz
                </button>
            </div>
        </form>
    </div>
@endsection