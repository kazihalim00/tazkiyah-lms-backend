<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CompanionController extends Controller
{

    public function sendRequest(Request $request)
    {
        $sender = Auth::user();
        $receiver = User::findOrFail($request->partner_id);


        if ($sender->gender !== $receiver->gender) {
            return back()->with('error', 'দ্বীনি মডারেটর হিসেবে জেন্ডার আলাদা হওয়ায় এই রিকোয়েস্টটি পাঠানো সম্ভব নয়।');
        }


        $sender->companions()->attach($receiver->id, ['status' => 'pending']);
        return back()->with('success', 'Companion request sent!');
    }

    public function acceptRequest($userId)
    {
        Auth::user()->companions()->updateExistingPivot($userId, ['status' => 'accepted']);
        return back()->with('success', 'Companion accepted!');
    }
}