<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'lms_module_id',
        'title',
        'content_type',
        'content_body',
        'media_url',
        'order'
    ];

    // Relationship: A lesson belongs to a specific module
    public function module()
    {
        return $this->belongsTo(LmsModule::class, 'lms_module_id');
    }
}