<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    /**
     * Display the community feed with all posts, likes, and comments.
     */
    public function index()
    {
        // Eager load relationships to optimize queries and avoid N+1 issues
        $posts = Post::with(['user', 'likes', 'comments.user'])->latest()->get();
        return view('feed.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        Post::create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'image' => $imagePath,
        ]);

        return back()->with('success', 'Your post has been published!');
    }

    /**
     * Toggle like/unlike for a specific post.
     */
    public function toggleLike($postId)
    {
        $userId = Auth::id();
        $existingLike = Like::where('user_id', $userId)->where('post_id', $postId)->first();

        if ($existingLike) {
            $existingLike->delete(); // Unlike if already liked
        } else {
            Like::create([
                'user_id' => $userId,
                'post_id' => $postId
            ]); // Like if not liked yet
        }

        return back();
    }

    /**
     * Store a comment for a specific post.
     */
    public function storeComment(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $postId,
            'content' => $request->input('content'),
        ]);

        return back()->with('success', 'Comment added successfully!');
    }
    // Add these inside the FeedController class

    /**
     * Toggle like for a specific comment.
     */
    public function toggleCommentLike($commentId)
    {
        $userId = \Auth::id();
        $existingLike = \App\Models\CommentLike::where('user_id', $userId)->where('comment_id', $commentId)->first();

        if ($existingLike) {
            $existingLike->delete();
        } else {
            \App\Models\CommentLike::create(['user_id' => $userId, 'comment_id' => $commentId]);
        }
        return back();
    }

    /**
     * Store a reply to an existing comment.
     */
    public function storeReply(Request $request, $commentId)
    {
        $request->validate(['content' => 'required|string|max:500']);
        $parentComment = \App\Models\Comment::findOrFail($commentId);

        \App\Models\Comment::create([
            'user_id' => \Auth::id(),
            'post_id' => $parentComment->post_id,
            'parent_id' => $commentId,
            'content' => $request->input('content'),
        ]);

        return back()->with('success', 'Reply added successfully!');
    }
}