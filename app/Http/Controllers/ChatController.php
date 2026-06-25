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

        // Get partners and attach their latest message for WhatsApp-like sorting
        $partners = User::whereIn('id', $partnerIds)->get();

        $activePartners = $partners->map(function ($partner) use ($user) {
            $latestMessage = \App\Models\PartnerMessage::where(function ($q) use ($user, $partner) {
                $q->where('sender_id', $user->id)->where('receiver_id', $partner->id);
            })->orWhere(function ($q) use ($user, $partner) {
                $q->where('sender_id', $partner->id)->where('receiver_id', $user->id);
            })->orderBy('created_at', 'desc')->first();

            // Notun duti property add kora hocche
            $partner->latest_message = $latestMessage ? $latestMessage->message : null;
            $partner->latest_message_time = $latestMessage ? $latestMessage->created_at : null;

            return $partner;
        })->sortByDesc('latest_message_time')->values(); // Ekdom last message onujayi sort kora holo

        // 2. Load conversation history if a specific partner is selected
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

        // Note: Apnar view er nam jodi 'messages' hoy tahole 'messages' e rakhben, 
        // ar 'chat.index' hole 'chat.index' rakhben.
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

    public function react(Request $request, $messageId)
    {
        $message = PartnerMessage::findOrFail($messageId);


        if ($message->sender_id != Auth::id() && $message->receiver_id != Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        $message->reaction = $request->reaction;
        $message->save();

        return response()->json(['success' => true]);
    }

}