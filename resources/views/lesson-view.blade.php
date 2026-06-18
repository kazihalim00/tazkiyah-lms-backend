@extends('layouts.app')

@section('title', $lesson->title . ' - Tazkiyah')
@section('header_title', 'Lesson Viewer')

@section('content')
    <div class="max-w-4xl mx-auto">

        <!-- Top Navigation -->
        <div class="flex items-center justify-between mb-6">
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-600 transition font-medium">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back to Curriculum
            </a>

            <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-3 py-1 rounded-md uppercase tracking-wider">
                Lesson
            </span>
        </div>

        <!-- Lesson Content Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">

            <!-- Video Player Placeholder -->
            @if(isset($lesson->video_url) && $lesson->video_url)
                <div class="aspect-video bg-gray-900 flex items-center justify-center relative">
                    <div class="text-center text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.91 11.672a.375.375 0 0 1 0 .656l-5.603 3.113a.375.375 0 0 1-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112Z" />
                        </svg>
                        <p class="font-medium">Video Player ({{ $lesson->video_url }})</p>
                    </div>
                </div>
            @else
                <div
                    class="min-h-[8rem] py-8 bg-gradient-to-r from-emerald-400 to-teal-500 flex items-center px-6 md:px-8 relative overflow-hidden rounded-t-2xl">
                    <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]">
                    </div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white z-10 leading-snug w-full">
                        {{ $lesson->title }}
                    </h1>
                </div>
            @endif

            <div class="p-6 md:p-8">
                <!-- Lesson Text -->
                <div class="prose max-w-none text-gray-600 leading-relaxed mb-8">
                    {!! nl2br(e($lesson->content ?: 'No detailed content has been provided.')) !!}
                </div>

                <!-- ================= DYNAMIC QUIZ SECTION ================= -->
                @if($lesson->quiz && $lesson->quiz->questions->count() > 0)
                    <div class="mt-12 border-t border-gray-100 pt-10">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="bg-indigo-100 text-indigo-600 p-2.5 rounded-xl shadow-inner">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl md:text-2xl font-black text-gray-800 tracking-tight">
                                    {{ $lesson->quiz->title }}</h3>
                                <p class="text-xs md:text-sm text-gray-500 font-medium">Test your understanding of this lesson.
                                </p>
                            </div>
                        </div>

                        <form action="{{ route('quizzes.submit', $lesson->quiz->id) }}" method="POST">
                            @csrf
                            @foreach($lesson->quiz->questions as $index => $question)
                                <div
                                    class="bg-gray-50 p-5 md:p-6 rounded-2xl border border-gray-100 mb-6 transition hover:border-indigo-100">
                                    <p class="font-bold text-gray-800 mb-4 text-base md:text-lg">{{ $index + 1 }}.
                                        {{ $question->question_text }}</p>
                                    <div class="grid sm:grid-cols-2 gap-3">
                                        @foreach($question->options as $option)
                                            <label
                                                class="flex items-center p-3.5 bg-white border border-gray-200 rounded-xl cursor-pointer hover:bg-indigo-50 hover:border-indigo-200 transition group">
                                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}"
                                                    required class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                                <span
                                                    class="ml-3 text-sm font-medium text-gray-700 group-hover:text-indigo-800">{{ $option->option_text }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                            <button type="submit"
                                class="w-full sm:w-auto px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg transition-all">
                                Submit Quiz Results
                            </button>
                        </form>
                    </div>
                @endif
                <!-- ================= END QUIZ SECTION ================= -->
            </div>
        </div>

        <!-- Completion Action -->
        <div
            class="bg-gray-50 border border-gray-200 rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h4 class="text-lg font-bold text-gray-800">Finished this lesson?</h4>
                <p class="text-gray-500 text-sm">Marking this as complete will update your course progress.</p>
            </div>
            @if(\App\Models\LessonCompletion::where('user_id', auth()->id())->where('lesson_id', $lesson->id)->exists())
                <div class="flex items-center gap-2 bg-emerald-100 text-emerald-700 px-6 py-3 rounded-xl font-bold">
                    Completed
                </div>
            @else
                <form action="{{ route('lesson.complete', $lesson->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-700 transition">Mark as
                        Complete</button>
                </form>
            @endif
        </div>
    </div>
@endsection