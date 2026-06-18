<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CloudinaryService; 

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();

     
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('image')) {
            try {
                $cloudinaryService = new CloudinaryService();
                $imageUrl = $cloudinaryService->uploadImage($request->file('image'));

                $user->image = $imageUrl;
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['image' => 'Cloudinary Upload Failed: ' . $e->getMessage()]);
            }
        }

       
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}