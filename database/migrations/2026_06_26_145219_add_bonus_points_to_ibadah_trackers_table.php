<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('ibadah_trackers', function (Blueprint $table) {
            $table->integer('bonus_points')->default(0)->after('khushu_level');
        });
    }

    public function down()
    {
        Schema::table('ibadah_trackers', function (Blueprint $table) {
            $table->dropColumn('bonus_points');
        });
    }
};