<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surah extends Model
{
    use HasFactory;

    protected $fillable = ['surah_no', 'name_arabic', 'name_bangla', 'name_english', 'revelation_type', 'total_ayahs'];

    public function ayahs()
    {
        return $this->hasMany(Ayah::class);
    }
}