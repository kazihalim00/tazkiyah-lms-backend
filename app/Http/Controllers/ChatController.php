<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PartnerMessage;
use App\Models\AccountabilityPartner;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Display the chat dashboard with active partners and messages.
     */
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

        // 2. Load conversation history if a specific partner is selected
        $messages = collect();
        $selectedPartner = null;

        if ($partnerId && in_array($partnerId, $partnerIds)) {
            $selectedPartner = User::findOrFail($partnerId);

            // Querying strictly from the brand new partner_messages table
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

    /**
     * Store and send a new message to an active partner.
     */
    public function sendMessage(Request $request, $partnerId)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        // Secure check to ensure they are actually connected partners before inserting
        $isPartner = AccountabilityPartner::where('status', 'accepted')
            ->where(function ($q) use ($partnerId) {
                $q->where('user_id', Auth::id())->where('partner_id', $partnerId);
            })->orWhere(function ($q) use ($partnerId) {
                $q->where('user_id', $partnerId)->where('partner_id', Auth::id());
            })->exists();

        if (!$isPartner) {
            return response()->json(['success' => false, 'message' => 'Unauthorized chat attempt.'], 403);
        }

        // Saving directly into the new partner_messages table
        PartnerMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $partnerId,
            'message' => $request->message
        ]);

        // AJAX এর জন্য JSON রেসপন্স পাঠাচ্ছি (আগের redirect রিমুভ করে দিয়েছি)
        return response()->json(['success' => true]);
    }
}