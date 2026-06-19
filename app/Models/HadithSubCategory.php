<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class HadithSubCategory extends Model
{
    protected $fillable = ['category_id', 'name_bn', 'name_en'];

    public function hadiths()
    {
        return $this->hasMany(Hadith::class, 'sub_category_id');
    }
    public function category()
    {
        return $this->belongsTo(HadithCategory::class, 'category_id');
    }
}