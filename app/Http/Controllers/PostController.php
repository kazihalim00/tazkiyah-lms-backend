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

        return view('feed.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            // ভিডিও ফরম্যাটও অ্যালাউ করা হলো (20MB Max)
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,webm|max:20480',
        ]);

        $mediaUrl = null;
        if ($request->hasFile('image')) {
            $mediaUrl = cloudinary()->upload($request->file('image')->getRealPath(), [
                'resource_type' => 'auto'
            ])->getSecurePath();
        }

        \App\Models\Post::create([
            'user_id' => auth()->id(),
            'content' => $request->get('content'),
            'image' => $mediaUrl,
        ]);

        return redirect()->back()->with('success', 'Post uploaded successfully!');
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