<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ayah extends Model
{
    use HasFactory;

    protected $fillable = ['surah_id', 'ayah_no', 'arabic_text', 'bangla_text', 'english_text', 'tafsir'];

    public function surah()
    {
        return $this->belongsTo(Surah::class);
    }
}