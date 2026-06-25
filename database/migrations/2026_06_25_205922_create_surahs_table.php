<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('surahs', function (Blueprint $table) {
            $table->id();
            $table->integer('surah_no')->unique(); 
            $table->string('name_arabic'); 
            $table->string('name_bangla'); 
            $table->string('name_english'); 
            $table->string('revelation_type'); 
            $table->integer('total_ayahs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surahs');
    }
};
