<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        if ($request->hasFile('image')) {
            // Render-এর লোকাল স্টোরেজের বদলে সরাসরি Cloudinary-তে আপলোড হচ্ছে
            $imageUrl = cloudinary()->upload($request->file('image')->getRealPath())->getSecurePath();
            $user->image = $imageUrl;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}