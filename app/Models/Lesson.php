<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'module_id',
        'title',
        'content',
        'content_type'
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function completedByUsers()
    {
        return $this->belongsToMany(User::class, 'lesson_completions');
    }
}