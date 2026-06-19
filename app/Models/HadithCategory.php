<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class HadithCategory extends Model
{
    protected $fillable = ['name_bn', 'name_en', 'slug'];

    public function hadiths()
    {
        return $this->hasMany(Hadith::class, 'category_id');
    }
}
