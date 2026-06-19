<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('hadith_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('hadith_categories')->onDelete('cascade');
            $table->string('name_bn'); 
            $table->string('name_en')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hadiths', function (Blueprint $table) {
            //
        });
    }
};
