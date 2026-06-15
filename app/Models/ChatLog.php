<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'user_message', 'ai_response', 'mood_tag'];

    // Relationship: A chat log belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}