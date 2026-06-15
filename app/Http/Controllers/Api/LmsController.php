<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LmsLevel;
use Illuminate\Http\Request;
use App\Models\Course;

class LmsController extends Controller
{
    public function getLevelsWithModules()
    {
        // Fetch levels with their associated modules using eager loading
        $levels = LmsLevel::with([
            'modules' => function ($query) {
                $query->orderBy('order', 'asc'); // Sort modules by order in ascending sequence
            }
        ])->get();

        // Fetch levels with their modules, and modules with their lessons
        $levels = LmsLevel::with([
            'modules' => function ($query) {
                $query->orderBy('order', 'asc');
            },
            'modules.lessons' => function ($query) {
                $query->orderBy('order', 'asc');
            }
        ])->get();

        // Return data in JSON format
        return response()->json([
            'success' => true,
            'message' => 'LMS Levels and Modules fetched successfully',
            'data' => $levels
        ], 200);
    }
    // App\Http\Controllers\LMSController.php
    public function index()
    {
        $courses = Course::all();
        return view('lms', compact('courses'));
    }
}