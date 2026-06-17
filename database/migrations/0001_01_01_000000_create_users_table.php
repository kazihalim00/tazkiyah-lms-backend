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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['user', 'admin', 'scholar'])->default('user');
            $table->string('timezone')->nullable();
            $table->integer('total_points')->default(0);

            $table->rememberToken();
            $table->timestamps();
        });

        // Migration for feed_posts
        Schema::create('feed_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('content');
            $table->string('type'); // 'hadith', 'ayah', 'general', 'help'
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        // Migration for companions
        Schema::create('companions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // মূল ইউজার
            $table->unsignedBigInteger('partner_id'); // সঙ্গী বা বন্ধু
            $table->string('status')->default('active'); // active, pending, blocked
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('partner_id')->references('id')->on('users');
        });
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
