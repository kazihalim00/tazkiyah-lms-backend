<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CloudinaryService; 

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();

        // ১. ডাটা ভ্যালিডেশন
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // ২. ইমেজ আপলোড হ্যান্ডল করা
        if ($request->hasFile('image')) {
            try {
                // আপনার সার্ভিসটি ব্যবহার করা ভালো, কারণ এটি আপনার কনফিগারেশন হ্যান্ডেল করছে
                $cloudinaryService = new CloudinaryService();
                $imageUrl = $cloudinaryService->uploadImage($request->file('image'));

                $user->image = $imageUrl;
            } catch (\Exception $e) {
                // যদি এরর হয় তবে ব্যবহারকারীকে জানান
                return redirect()->back()->withErrors(['image' => 'Cloudinary Upload Failed: ' . $e->getMessage()]);
            }
        }

       
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}