<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Module;

class LessonController extends Controller
{
    public function create()
    {
        $modules = Module::all();
        return view('admin.lessons.create', compact('modules'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'module_id' => 'required',
            'title' => 'required',
            'content' => 'required',
            'content_type' => 'required',
        ]);

        Lesson::create($validated);

        return redirect()->back()->with('success', 'Lesson added successfully!');
    }

    public function index()
    {

        $lessons = Lesson::with('module')->latest()->get();
        return view('admin.lessons.index', compact('lessons'));
    }


    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return redirect()->route('admin.lessons.index')->with('success', 'Lesson deleted successfully!');
    }

    public function edit(Lesson $lesson)
    {
        $modules = Module::all();
        return view('admin.lessons.edit', compact('lesson', 'modules'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        $validated = $request->validate([
            'module_id' => 'required',
            'title' => 'required',
            'content' => 'required',
            'content_type' => 'required',
        ]);

        $lesson->update($validated);

        return redirect()->route('admin.lessons.index')->with('success', 'Lesson updated successfully!');
    }
    public function show($id)
    {
        $lesson = Lesson::with('quiz.questions.options')->findOrFail($id);
        return view('lessons.show', compact('lesson'));
    }
}