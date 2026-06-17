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
        Schema::table('ibadah_trackers', function (Blueprint $table) {
            $table->string('fajr')->change();
            $table->string('dhuhr')->change();
            $table->string('asr')->change();
            $table->string('maghrib')->change();
            $table->string('isha')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ibadah_trackers', function (Blueprint $table) {
            //
        });
    }
};
