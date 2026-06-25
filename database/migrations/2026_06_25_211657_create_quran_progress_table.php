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
        Schema::create('quran_progresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('surah_id')->constrained()->onDelete('cascade');
            $table->foreignId('ayah_id')->constrained()->onDelete('cascade');

            $table->boolean('is_read')->default(false); 
            $table->text('tadabbur_note')->nullable(); 
            $table->integer('points_earned')->default(0);

            $table->timestamps();

            $table->unique(['user_id', 'ayah_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_progress');
    }
};
