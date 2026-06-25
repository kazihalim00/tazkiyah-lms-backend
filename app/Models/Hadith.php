<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hadith extends Model
{
    protected $fillable = [
        'category_id',
        'arabic_text',
        'sub_category_id',
        'bangla_text',
        'english_text',
        'book_name',
        'reference',
        'grade',
        'hadith_number',
        'explanation',
        'source_url',
        'points',
        'narrator'
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
    public function subCategory()
    {
        return $this->belongsTo(HadithSubCategory::class, 'sub_category_id');
    }
}