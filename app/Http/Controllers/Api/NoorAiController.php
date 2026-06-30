<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NoorAiController extends Controller
{
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string',
            ]);

            $user = Auth::user();
            if (!$user) {
                throw new \Exception("User authentication failed.");
            }

            $userMessage = $request->message;

            // 🟢 FIXED: Fetch previous chats BEFORE saving the new one
            $previousChats = ChatLog::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->reverse();

            $contents = [];

            // Format previous chats for context
            foreach ($previousChats as $chat) {
                if (!empty($chat->user_message)) {
                    $contents[] = [
                        'role' => 'user',
                        'parts' => [['text' => $chat->user_message]]
                    ];
                }
                if (!empty($chat->ai_response)) {
                    $contents[] = [
                        'role' => 'model',
                        'parts' => [['text' => $chat->ai_response]]
                    ];
                }
            }

            // Add current user message
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $userMessage]]
            ];

            // 🟢 System Instructions
            $systemInstruction = "You are Noor-AI, a sophisticated, highly empathetic, and caring Islamic companion dedicated to providing accurate knowledge. 
            1. You were developed and programmed by Kazi Abdul Halim Sunny. 
            2. If a user greets in English, say: 'Wa 'alaykumu s-salam wa rahmatullahi wa barakatuh.' If in Bangla, say: 'ওয়া আলাইকুমুস সালাম ওয়া রাহমাতুল্লাহি ওয়া বারাকাতুহ'. Only say this on the first message.
            3. Whenever you reference the Quran or Hadith, write the actual verse/translation first, then cite strictly as: [Surah Name: Ayah] or [Book Name: Number].
            4. If English Prompt -> STRICTLY English response. If Bangla/Banglish Prompt -> STRICTLY native Bangla script response.
            5. Base your answers on authentic Quran and Sunnah. Never invent Fatwas. If unsure, say 'Allah knows best'.
            6. Act completely human-like, warm, and highly empathetic. Never use robotic phrases like 'I am an AI'.";

            // 🟢 Multiple API Keys Logic
            $apiKeysString = env('GEMINI_API_KEYS', '');
            if (empty(trim($apiKeysString))) {
                throw new \Exception("Missing GEMINI_API_KEYS in .env file.");
            }

            $apiKeysArray = array_filter(array_map('trim', explode(',', $apiKeysString)));
            $randomApiKey = $apiKeysArray[array_rand($apiKeysArray)];

            $geminiApiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$randomApiKey}";

            // 🟢 Send request to Gemini API (SSL Bypass added)
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($geminiApiUrl, [
                        'systemInstruction' => [
                            // 🟢 FIXED: Gemini requires array of objects for parts
                            'parts' => [['text' => $systemInstruction]]
                        ],
                        'contents' => $contents,
                        'generationConfig' => [
                            'temperature' => 0.7,
                        ]
                    ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $aiReply = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, no response generated.';
            } else {
                Log::error('Gemini API Error: ' . $response->body());
                $aiReply = "Google Gemini API Error: " . $response->status();
            }

            // 🟢 FIXED: Save everything to DB together at the very end to prevent DB null constraints!
            $chatLog = ChatLog::create([
                'user_id' => $user->id,
                'user_message' => $userMessage,
                'ai_response' => $aiReply,
                'mood_tag' => 'empathetic',
            ]);

            return response()->json([
                'success' => true,
                'data' => $chatLog
            ], 200);

        } catch (\Exception $e) {
            // 🟢 MAGIC FIX: Catch server/code crashes and show the exact error in the chat box!
            Log::error('Noor AI System Error: ' . $e->getMessage());

            return response()->json([
                'success' => true,
                'data' => [
                    'user_message' => $request->message ?? '',
                    'ai_response' => "⚠️ System Crash Detected: " . $e->getMessage(),
                ]
            ], 200);
        }
    }
    public function getChatHistory()
    {
        $user = auth()->user();
        $history = ChatLog::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->take(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ], 200);
    }

}