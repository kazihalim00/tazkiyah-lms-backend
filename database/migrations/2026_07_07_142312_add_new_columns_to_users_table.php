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
        Schema::table('users', function (Blueprint $table) {
            // 🟢 Check if column exists before adding to prevent duplicate errors
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'image')) {
                $table->string('image')->nullable()->after('gender');
            }

            if (!Schema::hasColumn('users', 'last_read_ayah_id')) {
                $table->unsignedBigInteger('last_read_ayah_id')->nullable()->after('total_points');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'gender')) {
                $table->dropColumn('gender');
            }
            if (Schema::hasColumn('users', 'image')) {
                $table->dropColumn('image');
            }
            if (Schema::hasColumn('users', 'last_read_ayah_id')) {
                $table->dropColumn('last_read_ayah_id');
            }
        });
    }
};