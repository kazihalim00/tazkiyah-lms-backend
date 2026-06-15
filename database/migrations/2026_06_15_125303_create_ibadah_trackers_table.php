<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ibadah_trackers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->enum('fajr', ['Jamaah', 'Individual', 'Missed'])->default('Missed');
            $table->enum('dhuhr', ['Jamaah', 'Individual', 'Missed'])->default('Missed');
            $table->enum('asr', ['Jamaah', 'Individual', 'Missed'])->default('Missed');
            $table->enum('maghrib', ['Jamaah', 'Individual', 'Missed'])->default('Missed');
            $table->enum('isha', ['Jamaah', 'Individual', 'Missed'])->default('Missed');
            $table->boolean('morning_adhkar')->default(false);
            $table->boolean('evening_adhkar')->default(false);
            $table->boolean('quran_recitation')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ibadah_trackers');
    }
};
