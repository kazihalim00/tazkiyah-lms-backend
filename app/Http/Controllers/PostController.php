<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function index()
    {
        // 🟢 Get current user's gender (Fallback to male if null)
        $userGender = Auth::user()->gender ?? 'male';

        // 🟢 Fetch posts ONLY from users who match the current user's gender
        $posts = Post::with(['user', 'likes', 'comments.user', 'comments.replies.user', 'comments.likes'])
            ->whereHas('user', function ($query) use ($userGender) {
                $query->where('gender', $userGender);
            })
            ->latest()
            ->get();

        return view('feed.index', compact('posts'));
    }

    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'content' => 'required_without:image|nullable|string|max:5000',
            'image' => 'required_without:content|nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,webm|max:20480',
        ], [
            'content.required_without' => 'Please write something or upload a media file.',
            'image.required_without' => 'Please upload a media file or write something.'
        ]);

        $mediaUrl = null;
        if ($request->hasFile('image')) {
            try {
                $mediaUrl = $this->cloudinaryService->uploadImage($request->file('image'));
            } catch (\Exception $e) {
                return back()->withErrors(['image' => 'Failed to upload media. Please try again.']);
            }
        }

        Post::create([
            'user_id' => auth()->id(),
            // Fix: Save empty string instead of null if content is empty
            'content' => $request->get('content') ?? '',
            'image' => $mediaUrl,
        ]);

        return redirect()->route('feed.index')->with('success', 'Post published successfully!');
    }

    public function destroy(Post $post)
    {
        if (auth()->id() !== $post->user_id && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $post->delete();
        return back()->with('success', 'Post deleted successfully.');
    }

    public function toggleLike(Post $post)
    {
        $like = $post->likes()->where('user_id', auth()->id())->first();
        if ($like) {
            $like->delete();
        } else {
            $post->likes()->create(['user_id' => auth()->id()]);
        }
        return back();
    }

    public function storeComment(Request $request, Post $post)
    {
        $request->validate(['content' => 'required|string|max:1000']);
        $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->input('content')
        ]);
        return back();
    }

    public function toggleCommentLike(Comment $comment)
    {
        $like = $comment->likes()->where('user_id', auth()->id())->first();
        if ($like) {
            $like->delete();
        } else {
            $comment->likes()->create(['user_id' => auth()->id()]);
        }
        return back();
    }

    public function storeReply(Request $request, Comment $comment)
    {
        $request->validate(['content' => 'required|string|max:1000']);
        Comment::create([
            'post_id' => $comment->post_id,
            'user_id' => auth()->id(),
            'parent_id' => $comment->id,
            'content' => $request->input('content')
        ]);
        return back();
    }
}