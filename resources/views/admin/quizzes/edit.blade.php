@extends('layouts.app')

@section('title', 'Edit Quiz')
@section('header_title', 'Edit Quiz Details')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Quiz</h1>
        <a href="{{ route('admin.quizzes.index') }}" class="text-indigo-600 font-bold hover:underline">&larr; Back to Quizzes</a>
    </div>

    <form action="{{ route('admin.quizzes.update', $quiz->id) }}" method="POST" class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Quiz Title</label>
                <input type="text" name="title" value="{{ $quiz->title }}" required 
                    class="w-full border-gray-200 rounded-xl p-3 focus:ring-indigo-500 focus:border-indigo-500 text-gray-800">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" required 
                    class="w-full border-gray-200 rounded-xl p-3 focus:ring-indigo-500 focus:border-indigo-500 text-gray-800">{{ $quiz->description }}</textarea>
            </div>

            <hr class="border-gray-100 my-8">

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">Questions</h3>
                <button type="button" onclick="addQuestion()" 
                    class="bg-indigo-50 text-indigo-700 px-4 py-2 rounded-xl font-bold hover:bg-indigo-100 transition text-sm">
                    + Add New Question
                </button>
            </div>

            <div id="questions-container" class="space-y-6">
                @foreach($quiz->questions as $qIndex => $question)
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200 question-block">
                        <input type="hidden" name="questions[{{ $qIndex }}][id]" value="{{ $question->id }}">
                        
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-bold text-gray-700">Question {{ $loop->iteration }}</h4>
                            <button type="button" onclick="this.closest('.question-block').remove()" class="text-red-500 hover:text-red-700 font-bold text-sm">Remove</button>
                        </div>
                        
                        <input type="text" name="questions[{{ $qIndex }}][text]" value="{{ $question->question_text }}" required 
                            class="w-full border-gray-200 rounded-xl p-3 focus:ring-indigo-500 text-gray-800 font-medium">

                        <div class="space-y-3 mt-4 pl-4 border-l-2 border-indigo-100">
                            <p class="text-xs text-gray-500 font-bold mb-2">Options (Select the correct one)</p>
                            @foreach($question->options as $oIndex => $option)
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="correct_option[{{ $qIndex }}]" value="{{ $oIndex }}" 
                                        {{ $option->is_correct ? 'checked' : '' }} required 
                                        class="text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer">
                                    <input type="text" name="questions[{{ $qIndex }}][options][{{ $oIndex }}]" value="{{ $option->option_text }}" required 
                                        class="w-full border-gray-200 rounded-xl p-2 text-sm focus:ring-indigo-500">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-6 mt-6 border-t border-gray-100 flex justify-end">
                <button type="submit" class="bg-indigo-600 text-white px-10 py-3.5 rounded-xl font-black hover:bg-indigo-700 transition shadow-md">
                    Update Quiz
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // Starts from the current number of questions to avoid index overlap
    let questionIndex = {{ $quiz->questions->count() }};

    function addQuestion() {
        const container = document.getElementById('questions-container');
        const html = `
            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200 question-block">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-bold text-indigo-600">New Question</h4>
                    <button type="button" onclick="this.closest('.question-block').remove()" class="text-red-500 hover:text-red-700 font-bold text-sm">Remove</button>
                </div>
                
                <input type="text" name="questions[${questionIndex}][text]" placeholder="Type question here..." required 
                    class="w-full border-gray-200 rounded-xl p-3 focus:ring-indigo-500 font-medium">

                <div class="space-y-3 mt-4 pl-4 border-l-2 border-indigo-100">
                    <p class="text-xs text-gray-500 font-bold mb-2">Options (Select the correct one)</p>
                    ${[0, 1, 2, 3].map(i => `
                        <div class="flex items-center gap-3">
                            <input type="radio" name="correct_option[${questionIndex}]" value="${i}" required 
                                class="text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer">
                            <input type="text" name="questions[${questionIndex}][options][${i}]" placeholder="Option ${i + 1}" required 
                                class="w-full border-gray-200 rounded-xl p-2 text-sm focus:ring-indigo-500">
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        questionIndex++;
    }
</script>
@endsection