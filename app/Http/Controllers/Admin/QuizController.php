<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;

class QuizController extends Controller
{


    public function index()
    {
        $quizzes = Quiz::with('questions')->latest()->get();
        return view('admin.quizzes.index', compact('quizzes'));
    }


    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz deleted successfully!');
    }
    public function create()
    {
        return view('admin.quizzes.create');
    }

    public function store(Request $request)
    {
        // ১. কুইজ সেভ করা
        $quiz = Quiz::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        // ২. প্রশ্ন ও অপশনগুলো সেভ করা
        foreach ($request->questions as $qData) {
            $question = $quiz->questions()->create(['question_text' => $qData['text']]);

            foreach ($qData['options'] as $index => $optionText) {
                $question->options()->create([
                    'option_text' => $optionText,
                    'is_correct' => ($request->correct_option[$qData['id']] == $index)
                ]);
            }
        }

        return redirect()->back()->with('success', 'Quiz created successfully!');
    }
}