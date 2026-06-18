<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizResult;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function show($id)
    {
        $quiz = Quiz::with('questions.options')->findOrFail($id);
        return view('quizzes.show', compact('quiz'));
    }

    public function submit(Request $request, $id)
    {
        $quiz = Quiz::with('questions.options')->findOrFail($id);
        $score = 0;
        $total = count($request->answers);

        foreach ($request->answers as $questionId => $optionId) {
            $question = $quiz->questions()->find($questionId);
            if ($question && $question->options()->where('id', $optionId)->where('is_correct', true)->exists()) {
                $score++;
            }
        }

        QuizResult::create([
            'user_id' => auth()->id(),
            'quiz_id' => $quiz->id,
            'score' => $score,
            'total_marks' => $quiz->questions->count()
        ]);

        return redirect()->route('quizzes.show', $id)->with('success', "You scored $score out of " . $quiz->questions->count());
    }
}