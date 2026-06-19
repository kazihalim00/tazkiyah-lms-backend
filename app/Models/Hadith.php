<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hadith extends Model
{
    protected $fillable = [
        'category_id',
        'arabic_text',
        'bangla_text',
        'english_text',
        'reference',
        'grade',
        'explanation',
        'source_url',
        'points'
    ];

    public function category()
    {
        return $this->belongsTo(HadithCategory::class);
    }

    public function readers()
    {
        return $this->hasMany(UserHadithProgress::class);
    }

    public function isReadBy($userId)
    {
        return $this->readers()->where('user_id', $userId)->exists();
    }
}