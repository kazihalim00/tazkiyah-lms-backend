<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuranProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'surah_id',
        'ayah_id',
        'is_read',
        'tadabbur_note',
        'points_earned'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ayah()
    {
        return $this->belongsTo(Ayah::class);
    }

    public function surah()
    {
        return $this->belongsTo(Surah::class);
    }
}