<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IbadahTracker extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'fajr',
        'dhuhr',
        'asr',
        'maghrib',
        'isha',
        'morning_adhkar',
        'evening_adhkar',
        'quran_recitation'
    ];

    // Relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}