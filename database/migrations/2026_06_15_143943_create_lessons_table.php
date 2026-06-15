<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            // Link to the specific module
            $table->foreignId('lms_module_id')->constrained('lms_modules')->cascadeOnDelete();

            $table->string('title');
            // Determine the type of the lesson
            $table->enum('content_type', ['text', 'video', 'quiz'])->default('text');

            // For reading materials or articles
            $table->longText('content_body')->nullable();

            // For YouTube/Vimeo links or PDF URLs
            $table->string('media_url')->nullable();

            // Keep lessons in a specific order
            $table->integer('order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};