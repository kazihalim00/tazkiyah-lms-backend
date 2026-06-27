<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller
{
    // Show the manual donation form
    public function index()
    {
        return view('donate.index');
    }

    // Process the manual donation submission
    public function pay(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10',
            'donation_sector' => 'required|string',
            'trx_id' => 'required|string|unique:donations,trx_id',
            'sender_number' => 'required|string|min:11',
        ]);

        // Save manual payment info into database as 'pending'
        Donation::create([
            'user_id' => Auth::id(), // Null if guest user
            'amount' => $request->amount,
            'donation_sector' => $request->donation_sector,
            'trx_id' => $request->trx_id,
            'payment_id' => $request->sender_number, // Storing sender's phone number in payment_id column
            'payment_status' => 'pending',
        ]);

        // Return with success message for the user
        return back()->with('success', 'Alhamdulillah! Your donation details have been submitted. We will verify and approve it shortly.');
    }
}