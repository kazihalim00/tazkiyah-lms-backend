<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('hadiths', function (Blueprint $table) {
            if (!Schema::hasColumn('hadiths', 'book_name')) {
                $table->string('book_name')->nullable()->after('id');
            }
        });
    }

    public function down()
    {
        Schema::table('hadiths', function (Blueprint $table) {
            if (Schema::hasColumn('hadiths', 'book_name')) {
                $table->dropColumn('book_name');
            }
        });
    }
};