@extends('layouts.app')
@section('content')
    <div class="max-w-4xl mx-auto py-10">
        <h1 class="text-2xl font-bold mb-6">Create New Quiz</h1>

        <form action="{{ route('admin.quizzes.store') }}" method="POST" id="quizForm">
            @csrf
            <div class="bg-white p-6 rounded-xl shadow mb-6">
                <input type="text" name="title" placeholder="Quiz Title" class="w-full mb-4 p-2 border rounded" required>
                <textarea name="description" placeholder="Description" class="w-full p-2 border rounded"></textarea>
            </div>

            <div id="questionsContainer">
            </div>

            <button type="button" onclick="addQuestion()" class="bg-emerald-600 text-white px-4 py-2 rounded mb-6">
                + Add Question
            </button>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded">Save Quiz</button>
        </form>
    </div>

    <script>
        let qCount = 0;
        function addQuestion() {
            qCount++;
            const container = document.getElementById('questionsContainer');
            container.innerHTML += `
                    <div class="bg-gray-50 p-4 rounded mb-4 border">
                        <input type="text" name="questions[${qCount}][text]" placeholder="Question ${qCount}" class="w-full mb-2 p-2 border rounded" required>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" name="questions[${qCount}][options][]" placeholder="Option A" class="p-2 border rounded" required>
                            <input type="text" name="questions[${qCount}][options][]" placeholder="Option B" class="p-2 border rounded" required>
                            <input type="text" name="questions[${qCount}][options][]" placeholder="Option C" class="p-2 border rounded" required>
                            <input type="text" name="questions[${qCount}][options][]" placeholder="Option D" class="p-2 border rounded" required>
                        </div>
                        <label class="block mt-2">Correct Option Index (0 for A, 1 for B...):</label>
                        <input type="number" name="correct_option[${qCount}]" min="0" max="3" class="w-20 p-2 border rounded" required>
                    </div>
                `;
        }
    </script>
@endsection