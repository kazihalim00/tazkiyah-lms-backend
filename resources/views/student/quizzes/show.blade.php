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
                                @php
                                    // সাবমিট করার পর সাকসেস সেশন থাকলে এবং উত্তরটি সঠিক হলে ব্যাকগ্রাউন্ড সবুজ হবে
                                    $isCorrect = session('success') && $option->is_correct;
                                    $bgClass = $isCorrect ? 'bg-emerald-100 border-emerald-500' : 'bg-gray-50 border-transparent';
                                    $textClass = $isCorrect ? 'text-emerald-800 font-bold' : 'text-gray-700 font-medium';
                                    
                                    // সাবমিট করার পর hover ইফেক্ট আর কার্সর পয়েন্টার বন্ধ করে দেওয়া
                                    $hoverClass = session('success') ? 'cursor-default' : 'cursor-pointer hover:bg-gray-50 hover:border-gray-100';
                                @endphp

                                <label
                                    class="flex items-center gap-3 p-3 rounded-xl border {{ $bgClass }} {{ $hoverClass }} transition">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}"
                                        class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 focus:ring-indigo-500"
                                        {{ session('success') ? 'disabled' : 'required' }}>
                                    
                                    <span class="{{ $textClass }}">
                                        {{ $option->option_text }}

                                        @if($isCorrect)
                                            <svg class="w-5 h-5 inline-block ml-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex justify-end">
                @if(!session('success'))
                    <button type="submit"
                        class="bg-indigo-600 text-white px-10 py-3.5 rounded-xl font-black hover:bg-indigo-700 transition shadow-md text-lg">
                        Submit Quiz
                    </button>
                @else
                    <a href="{{ url('/my-dashboard') }}"
                        class="bg-gray-800 text-white px-10 py-3.5 rounded-xl font-black hover:bg-gray-700 transition shadow-md text-lg">
                        Back to Dashboard
                    </a>
                @endif
            </div>
            
        </form>
    </div>
@endsection