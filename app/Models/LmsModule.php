<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsModule extends Model
{
    use HasFactory;

    protected $fillable = ['lms_level_id', 'title', 'order'];

    // Relationship
    public function level()
    {
        return $this->belongsTo(LmsLevel::class, 'lms_level_id');
    }
}