@extends('layouts.app')
@section('content')
    <div class="max-w-3xl mx-auto py-10">
        <h1 class="text-3xl font-bold mb-6">{{ $quiz->title }}</h1>

        @if(session('success'))
            <div class="bg-indigo-600 text-white p-4 rounded-xl mb-6 font-bold text-center">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('quizzes.submit', $quiz->id) }}" method="POST">
            @csrf
            @foreach($quiz->questions as $index => $question)
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
                    <p class="font-bold text-gray-800 mb-4">{{ $index + 1 }}. {{ $question->question_text }}</p>
                    <div class="space-y-2">
                        @foreach($question->options as $option)
                            <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" required
                                    class="mr-3">
                                {{ $option->option_text }}
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
            <button type="submit"
                class="bg-indigo-600 text-white w-full py-4 rounded-xl font-bold hover:bg-indigo-700">Submit Quiz</button>
        </form>
    </div>
@endsection