<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = ['module_id', 'title', 'content', 'content_type', 'order'];    // Relationship: A lesson belongs to a specific module
    public function module()
    {
        return $this->belongsTo(LmsModule::class, 'lms_module_id');
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lesson) {
            if (empty($lesson->lms_module_id)) {
                $lesson->lms_module_id = $lesson->module_id;
            }
        });
    }
    
}