<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    // এই কলামগুলোতে একবারে ডাটা অ্যাসাইন করার অনুমতি দেওয়া হলো
    protected $fillable = [
        'title',
        'description'
    ];


    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}