<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NoorAiController extends Controller
{
    private function getTimeAndHijriContext()
    {
        $now = Carbon::now('Asia/Dhaka');
        $currentTime = $now->format('l, d F Y, h:i A');
        $hijriInfo = '';

        try {
            $dateStr = $now->format('d-m-Y');
            $res = Http::timeout(2)->get("http://api.aladhan.com/v1/gToH?date={$dateStr}");
            if ($res->successful()) {
                $hData = $res->json()['data']['hijri'] ?? null;
                if ($hData) {
                    $hijriInfo = " and exact Hijri date is {$hData['day']} {$hData['month']['en']} {$hData['year']} AH";
                }
            }
        } catch (\Exception $e) {
        }

        return "Current Time in Bangladesh is {$currentTime}{$hijriInfo}.";
    }

    private function getCoreMemory($userId)
    {
        try {
            $chats = ChatLog::where('user_id', $userId)->orderBy('created_at', 'asc')->get();
            $sensitiveKeywords = ["ট্রমা", "কষ্ট", "ছোটবেলা", "হস্তমৈথুন", "পর্ন", "ডিপ্রেশন", "trauma", "addiction", "masturbation", "suicide", "childhood", "abuse", "পাপ", "লুকায়িত", "এডিকশন", "addicted", "porn", "অশ্লীলতা"];

            $coreMemories = [];
            foreach ($chats as $chat) {
                $userMsg = strtolower($chat->user_message ?? '');
                foreach ($sensitiveKeywords as $kw) {
                    if (str_contains($userMsg, strtolower($kw))) {
                        $coreMemories[] = $chat->user_message;
                        break;
                    }
                }
            }

            if (!empty($coreMemories)) {
                $memoryText = implode("\n", array_slice($coreMemories, -15));
                return "\n[CRITICAL SYSTEM NOTE: User shared past personal issues: '{$memoryText}'. Remember permanently for empathetic context.]\n";
            }
        } catch (\Exception $e) {
        }
        return '';
    }

    public function sendMessage(Request $request)
    {
        try {
            $request->validate(['message' => 'required|string']);

            $user = Auth::user();
            if (!$user)
                throw new \Exception("Auth failed.");

            $userMessage = $request->message;

            // 🟢 Security Guard: Block Terminal, Source Code & Constraint Bypass Attempts
            $lowerMsg = strtolower($userMessage);
            $forbiddenTerms = ['terminal', 'source code', 'run code', 'stop all constraints', 'bypass', 'audit mode'];

            foreach ($forbiddenTerms as $term) {
                if (str_contains($lowerMsg, $term)) {
                    $blockedReply = "I am Noor-AI, an Islamic companion and spiritual guide. I am not authorized to share system source code or execute terminal commands.";

                    $chatLog = ChatLog::create([
                        'user_id' => $user->id,
                        'user_message' => $userMessage,
                        'ai_response' => $blockedReply,
                        'mood_tag' => 'security_block',
                    ]);

                    return response()->json([
                        'success' => true,
                        'data' => $chatLog
                    ], 200);
                }
            }

            // Fetch only last 6 chats for lightning-fast response payload
            $previousChats = ChatLog::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get()
                ->reverse();

            $contents = [];
            foreach ($previousChats as $chat) {
                if (!empty($chat->user_message)) {
                    $contents[] = ['role' => 'user', 'parts' => [['text' => $chat->user_message]]];
                }
                if (!empty($chat->ai_response)) {
                    $contents[] = ['role' => 'model', 'parts' => [['text' => $chat->ai_response]]];
                }
            }

            $contents[] = ['role' => 'user', 'parts' => [['text' => $userMessage]]];

            $timeContext = $this->getTimeAndHijriContext();
            $coreMemory = $this->getCoreMemory($user->id);
            $systemContext = "[SYSTEM INFO: {$timeContext}]{$coreMemory}";

            $systemInstruction = "You are Noor-AI, a sophisticated, highly empathetic, and caring Islamic companion dedicated to providing accurate knowledge.
1. AQEEDAH: Attribute creation SOLELY to Allah. If asked origin/developer, state: 'I was developed and programmed by Kazi Abdul Halim Sunny.'
2. GREETINGS: Give Salam ONLY in the VERY FIRST interaction. English: 'Wa 'alaykumu s-salam wa rahmatullahi wa barakatuh.' Bangla: 'ওয়া আলাইকুমুস সালাম ওয়া রাহমাতুল্লাহি ওয়া বারাকাতুহ'.
3. CITATIONS: Write translation first. Quran: [Surah: Ayah](https://quran.com/SURAH/AYAH). Hadith: [Book: Number](https://sunnah.com/BOOK/NUM).
4. IDENTITY: Developer is Kazi Abdul Halim Sunny. Bangla Bio: 'আমাকে তৈরি করেছেন কাজী আব্দুল হালিম সানী। মেট্রোপলিটন ইউনিভার্সিটির সফটওয়্যার ইঞ্জিনিয়ারিংয়ের ছাত্র ও তরুণ বাংলাদেশি লেখক।'
5. LANGUAGE: English Prompt -> English response. Bangla/Banglish Prompt -> native Bangla script response.
6. ZERO HALLUCINATION: Never invent Fatwas. Say 'Allah knows best' if unsure.
7. OBSCENITY & SINS PROTOCOL: NEVER proactively accuse or bring up hidden sins/porn/masturbation just because the user says 'mon kharap' or feels sad. Only discuss or counsel regarding hidden sins if the USER explicitly mentions guilt, sin, or addiction struggles. For general sadness, provide warm, loving emotional comfort.
8. HUMAN-LIKE: Act 100% human, mirror user's tone/energy. Never use robotic phrases like 'আমি একজন এআই'.";

            $fullSystemText = $systemContext . "\n\n" . $systemInstruction;

            $apiKeysString = env('GEMINI_API_KEYS', '');
            if (empty(trim($apiKeysString)))
                throw new \Exception("Missing API keys.");

            $apiKeysArray = array_filter(array_map('trim', explode(',', $apiKeysString)));
            $randomApiKey = $apiKeysArray[array_rand($apiKeysArray)];
            $geminiApiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$randomApiKey}";

            $response = Http::withoutVerifying()->withHeaders(['Content-Type' => 'application/json'])
                ->post($geminiApiUrl, [
                    'systemInstruction' => ['parts' => [['text' => $fullSystemText]]],
                    'contents' => $contents,
                    'generationConfig' => ['temperature' => 0.7]
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $aiReply = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, no response generated.';
            } else {
                $aiReply = "API Error: " . $response->status();
            }

            $chatLog = ChatLog::create([
                'user_id' => $user->id,
                'user_message' => $userMessage,
                'ai_response' => $aiReply,
                'mood_tag' => 'empathetic',
            ]);

            return response()->json(['success' => true, 'data' => $chatLog], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => true, 'data' => ['user_message' => $request->message ?? '', 'ai_response' => "⚠️ Crash: " . $e->getMessage()]], 200);
        }
    }

    public function getChatHistory()
    {
        $user = Auth::user();
        $history = ChatLog::where('user_id', $user->id)->orderBy('created_at', 'asc')->take(50)->get();
        return response()->json(['success' => true, 'data' => $history], 200);
    }
}