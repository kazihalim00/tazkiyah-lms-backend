<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ayahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surah_id')->constrained()->onDelete('cascade');
            $table->integer('ayah_no');
            $table->text('arabic_text');
            $table->text('bangla_text');
            $table->text('english_text')->nullable();
            $table->text('tafsir')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ayahs');
    }
};
