<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;

class StudentQuizController extends Controller
{
    public function show($id)
    {
        $quiz = Quiz::with('questions.options')->findOrFail($id);

        return view('student.quizzes.show', compact('quiz'));
    }

    public function submit(Request $request, $id)
    {

        return redirect()->back()->with('success', 'Alhamdulillah! Your quiz has been submitted successfully.');
    }
}