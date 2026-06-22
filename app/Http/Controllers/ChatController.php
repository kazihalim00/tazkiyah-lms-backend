<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PartnerMessage;
use App\Models\AccountabilityPartner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; 

class ChatController extends Controller
{
    public function index($partnerId = null)
    {
        $user = Auth::user();

        // 1. Fetch only verified/accepted active partners
        $connections = AccountabilityPartner::where('status', 'accepted')
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('partner_id', $user->id);
            })->get();

        $partnerIds = [];
        foreach ($connections as $conn) {
            $partnerIds[] = ($conn->user_id == $user->id) ? $conn->partner_id : $conn->user_id;
        }

        $activePartners = User::whereIn('id', $partnerIds)->get();

        $messages = collect();
        $selectedPartner = null;

        if ($partnerId && in_array($partnerId, $partnerIds)) {
            $selectedPartner = User::findOrFail($partnerId);

            $messages = PartnerMessage::where(function ($q) use ($user, $partnerId) {
                $q->where('sender_id', $user->id)->where('receiver_id', $partnerId);
            })->orWhere(function ($q) use ($user, $partnerId) {
                $q->where('sender_id', $partnerId)->where('receiver_id', $user->id);
            })->orderBy('created_at', 'asc')->get();

            // Mark unread messages from this partner as read
            PartnerMessage::where('sender_id', $partnerId)->where('receiver_id', $user->id)->update(['is_read' => true]);
        }

        return view('chat.index', compact('activePartners', 'selectedPartner', 'messages'));
    }

    public function sendMessage(Request $request, $partnerId)
    {
       
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()->first()]);
        }

        $userId = Auth::id();


        $isPartner = AccountabilityPartner::where('status', 'accepted')
            ->where(function ($query) use ($userId, $partnerId) {
                $query->where(function ($q1) use ($userId, $partnerId) {
                    $q1->where('user_id', $userId)->where('partner_id', $partnerId);
                })->orWhere(function ($q2) use ($userId, $partnerId) {
                    $q2->where('user_id', $partnerId)->where('partner_id', $userId);
                });
            })->exists();

        if (!$isPartner) {
            return response()->json(['success' => false, 'error' => 'Unauthorized chat attempt.']);
        }


        $message = PartnerMessage::create([
            'sender_id' => $userId,
            'receiver_id' => $partnerId,
            'message' => $request->message
        ]);

        return response()->json(['success' => true]);
    }
}