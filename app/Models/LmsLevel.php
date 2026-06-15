<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsLevel extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description'];

    // Relationship
    public function modules()
    {
        return $this->hasMany(LmsModule::class);
    }
}