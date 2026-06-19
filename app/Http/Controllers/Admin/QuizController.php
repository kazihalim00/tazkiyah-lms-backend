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
    public function show($id)
    {
        $quiz = Quiz::with('questions.options')->findOrFail($id);
        return view('admin.quizzes.show', compact('quiz'));
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
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);


        $quiz = \App\Models\Quiz::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);


        if ($request->has('questions')) {

            foreach ($request->questions as $qIndex => $qData) {

                $question = $quiz->questions()->create([
                    'question_text' => $qData['text']
                ]);

                foreach ($qData['options'] as $oIndex => $optionText) {
                    $question->options()->create([
                        'option_text' => $optionText,
                        // $qData['id'] এর বদলে $qIndex ব্যবহার করা হলো
                        'is_correct' => (isset($request->correct_option[$qIndex]) && $request->correct_option[$qIndex] == $oIndex)
                    ]);
                }
            }
        }

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz created successfully!');
    }

    public function edit($id)
    {
        $quiz = \App\Models\Quiz::findOrFail($id);
        return view('admin.quizzes.edit', compact('quiz'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $quiz = \App\Models\Quiz::findOrFail($id);
        $quiz->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);


        if ($request->has('questions')) {
            foreach ($request->questions as $qIndex => $qData) {


                if (isset($qData['id'])) {
                    $question = $quiz->questions()->find($qData['id']);
                    if ($question) {
                        $question->update(['question_text' => $qData['text']]);

                        $question->options()->delete();
                        foreach ($qData['options'] as $oIndex => $optionText) {
                            $question->options()->create([
                                'option_text' => $optionText,
                                'is_correct' => (isset($request->correct_option[$qIndex]) && $request->correct_option[$qIndex] == $oIndex)
                            ]);
                        }
                    }
                } else {
                    $question = $quiz->questions()->create([
                        'question_text' => $qData['text']
                    ]);

                    foreach ($qData['options'] as $oIndex => $optionText) {
                        $question->options()->create([
                            'option_text' => $optionText,
                            'is_correct' => (isset($request->correct_option[$qIndex]) && $request->correct_option[$qIndex] == $oIndex)
                        ]);
                    }
                }
            }
        }


        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz details updated successfully!');
    }
}