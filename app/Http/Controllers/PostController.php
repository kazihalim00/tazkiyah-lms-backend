<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\CloudinaryService;

class PostController extends Controller
{
    protected $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    public function index()
    {
        $posts = Post::with(['user', 'likes', 'comments'])->latest()->get();


        if (!view()->exists('feed')) {
            dd("Error: 'feed.blade.php' file not found in resources/views/");
        }

 
        return view('community', compact('posts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // ৫ মেগাবাইট লিমিট
        ]);

        $imageUrl = null;

        if ($request->hasFile('image')) {
            $imageUrl = $this->cloudinary->uploadImage($request->file('image'));
        }

        Post::create([
            'user_id' => auth()->id(),
            'content' => $request->input('content'),
            'image' => $imageUrl,
        ]);

        return redirect()->back()->with('success', 'Post created successfully!');
    }


    public function destroy(Post $post)
    {

        if (auth()->id() !== $post->user_id && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $post->delete();
        return redirect()->back()->with('success', 'Post deleted successfully!');
    }
}