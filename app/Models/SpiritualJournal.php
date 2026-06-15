<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpiritualJournal extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'date', 'entry_text'];

    // Relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}