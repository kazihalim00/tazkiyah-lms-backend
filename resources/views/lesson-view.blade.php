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

            <!-- Video Player Placeholder (If video URL exists) -->
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
                <!-- Reading Material Header (Fixed for Mobile) -->
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
                @if(isset($lesson->video_url) && $lesson->video_url)
                    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ $lesson->title }}</h1>
                @endif

                <!-- Lesson Text / Content -->
                <div class="prose max-w-none text-gray-600 leading-relaxed mb-8">
                    @php
                        $content = $lesson->content ?? '';
                        $isUrl = filter_var(trim($content), FILTER_VALIDATE_URL);
                    @endphp

                    @if($isUrl)
                        <div class="py-4">
                            <a href="{{ trim($content) }}" target="_blank"
                                class="inline-flex items-center gap-2 bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-emerald-700 transition shadow-sm w-full sm:w-auto justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Watch Video / Open Resource
                            </a>
                        </div>
                    @else
                        {!! nl2br(e($content ?: 'No detailed content has been provided for this lesson yet.')) !!}
                    @endif
                </div>

                <!-- ================= QUIZ SECTION ================= -->
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
                            <h3 class="text-xl md:text-2xl font-black text-gray-800 tracking-tight">Knowledge Check</h3>
                            <p class="text-xs md:text-sm text-gray-500 font-medium">Test your understanding of this lesson.
                            </p>
                        </div>
                    </div>

                    <form action="#" method="POST" class="space-y-6">
                        @csrf

                        <!-- 10 Questions Loop -->
                        @for ($i = 1; $i <= 10; $i++)
                            <div
                                class="bg-gray-50 p-5 md:p-6 rounded-2xl border border-gray-100 transition hover:shadow-md hover:border-indigo-100">
                                <p class="font-bold text-gray-800 mb-4 text-base md:text-lg">{{ $i }}. এই লেসনের গুরুত্বপূর্ণ
                                    প্রশ্ন {{ $i }} এখানে থাকবে?</p>
                                <div class="grid sm:grid-cols-2 gap-3">
                                    <label
                                        class="flex items-center p-3.5 bg-white border border-gray-200 rounded-xl cursor-pointer hover:bg-indigo-50 hover:border-indigo-200 transition group">
                                        <input type="radio" name="q{{ $i }}" value="A"
                                            class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                        <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-indigo-800">ক)
                                            প্রথম অপশন</span>
                                    </label>
                                    <label
                                        class="flex items-center p-3.5 bg-white border border-gray-200 rounded-xl cursor-pointer hover:bg-indigo-50 hover:border-indigo-200 transition group">
                                        <input type="radio" name="q{{ $i }}" value="B"
                                            class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                        <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-indigo-800">খ)
                                            দ্বিতীয় অপশন</span>
                                    </label>
                                    <label
                                        class="flex items-center p-3.5 bg-white border border-gray-200 rounded-xl cursor-pointer hover:bg-indigo-50 hover:border-indigo-200 transition group">
                                        <input type="radio" name="q{{ $i }}" value="C"
                                            class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                        <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-indigo-800">গ)
                                            তৃতীয় অপশন</span>
                                    </label>
                                    <label
                                        class="flex items-center p-3.5 bg-white border border-gray-200 rounded-xl cursor-pointer hover:bg-indigo-50 hover:border-indigo-200 transition group">
                                        <input type="radio" name="q{{ $i }}" value="D"
                                            class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                        <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-indigo-800">ঘ)
                                            চতুর্থ অপশন</span>
                                    </label>
                                </div>
                            </div>
                        @endfor

                        <div class="pt-4 pb-2">
                            <button type="button"
                                onclick="alert('আলহামদুলিল্লাহ! ডিজাইন রেডি। ডাটাবেজে কুইজ টেবিল বানালেই এটি পুরোপুরি কাজ করবে!')"
                                class="w-full sm:w-auto px-8 py-3.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2">
                                Submit Quiz Results
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
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

            @php
                $isCompleted = \App\Models\LessonCompletion::where('user_id', auth()->id())
                    ->where('lesson_id', $lesson->id)
                    ->exists();
            @endphp

            @if($isCompleted)
                <div class="flex items-center gap-2 bg-emerald-100 text-emerald-700 px-6 py-3 rounded-xl font-bold">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    Completed
                </div>
            @else
                <form action="{{ route('lesson.complete', $lesson->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-700 transition shadow-sm w-full sm:w-auto">
                        Mark as Complete
                    </button>
                </form>
            @endif
        </div>

    </div>
@endsection