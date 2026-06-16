<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\Course;

class ModuleController extends Controller
{

    public function index()
    {

        $modules = Module::with('course')->latest()->get();
        return view('admin.modules.index', compact('modules'));
    }


    public function create()
    {
        $courses = Course::all();
        return view('admin.modules.create', compact('courses'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required',
            'title' => 'required|string|max:255',
        ]);


        $validated['order'] = Module::where('course_id', $validated['course_id'])->count() + 1;

        Module::create($validated);

        return redirect()->route('admin.modules.index')->with('success', 'Module created successfully!');
    }
}