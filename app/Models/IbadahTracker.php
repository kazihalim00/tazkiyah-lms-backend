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
        'khushu_level',
        'morning_adhkar',
        'evening_adhkar',
        'tahajjud',
        'witr',
        'quran_pages',
        'sadaqah',
        'journal_notes'
    ];
    // Relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}