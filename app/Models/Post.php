<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'content', 'image'];

    /**
     * Get the user who created the post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Add these methods inside the Post class

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Check if the post is already liked by a specific user.
     */
    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }
    public function getImageUrlAttribute()
    {
        if (!$this->image)
            return null;
        return str_starts_with($this->image, 'http') ? $this->image : asset('storage/' . $this->image);
    }
}