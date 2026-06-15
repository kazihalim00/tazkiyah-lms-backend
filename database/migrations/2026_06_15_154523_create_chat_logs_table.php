<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_logs', function (Blueprint $table) {
            $table->id();
            // Link to the user who is chatting
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // The message sent by the user
            $table->text('user_message');

            // The reply from Noor AI
            $table->longText('ai_response')->nullable();

            // For mood-based interventions (e.g., 'sad', 'lazy', 'angry')
            $table->string('mood_tag')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_logs');
    }
};