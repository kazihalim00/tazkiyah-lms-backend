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
        Schema::table('ibadah_trackers', function (Blueprint $table) {
            // Adding the missing duwa column to the table
            $table->boolean('duwa')->default(0)->after('sadaqah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ibadah_trackers', function (Blueprint $table) {
            // Dropping the duwa column if rolled back
            $table->dropColumn('duwa');
        });
    }
};