<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // Required for making API requests to Python

class NoorAiController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $user = Auth::user();
        $userMessage = $request->message;

        // Step 1: Save the user's message to the database first
        $chatLog = ChatLog::create([
            'user_id' => $user->id,
            'user_message' => $userMessage,
        ]);

        try {
            // Step 2: Send request to your Python/Streamlit Noor AI API
            // Replace this URL with your actual Python API endpoint later
            $pythonApiUrl = 'http://127.0.0.1:5000/api/chat';

            $response = Http::post($pythonApiUrl, [
                'user_id' => $user->id,
                'message' => $userMessage
            ]);

            if ($response->successful()) {
                $aiData = $response->json();

                // Step 3: Update the chat log with AI's response and mood tag
                $chatLog->update([
                    'ai_response' => $aiData['response'] ?? 'I am here to listen.',
                    'mood_tag' => $aiData['mood'] ?? null,
                ]);
            } else {
                $chatLog->update([
                    'ai_response' => 'Sorry, Noor AI is currently taking a break. Please try again later.'
                ]);
            }

        } catch (\Exception $e) {
            // Fallback if the Python server is down
            $chatLog->update([
                'ai_response' => 'Connection to Noor AI failed. Please ensure the AI server is running.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $chatLog
        ], 200);
    }

    public function getChatHistory()
    {
        $user = Auth::user();
        $history = ChatLog::where('user_id', $user->id)->orderBy('created_at', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ], 200);
    }
}