<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hadith extends Model
{
    use HasFactory;

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

    /**
     * Relationship: Hadith category
     */
    public function category()
    {
        return $this->belongsTo(HadithCategory::class);
    }

    /**
     * Relationship: Hadith sub-category
     */
    public function subCategory()
    {
        return $this->belongsTo(HadithSubCategory::class, 'sub_category_id');
    }

    /**
     * Relationship: Users who have progressed/read this Hadith
     */
    public function userProgress()
    {
        return $this->hasMany(UserHadithProgress::class, 'hadith_id');
    }

    /**
     * Check if a specific user has read this Hadith
     */
    public function isReadBy($userId)
    {
        return $this->userProgress()->where('user_id', $userId)->exists();
    }
}