<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LmsLevel;
use Illuminate\Http\Request;

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

        // Return data in JSON format
        return response()->json([
            'success' => true,
            'message' => 'LMS Levels and Modules fetched successfully',
            'data' => $levels
        ], 200);
    }
}