<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserHadithProgress extends Model
{
    protected $table = 'user_hadith_progress';
    protected $fillable = ['user_id', 'hadith_id'];
}