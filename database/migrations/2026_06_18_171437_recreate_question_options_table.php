<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // ১. আগের ভুল টেবিলটি থাকলে মুছে ফেলবে
        Schema::dropIfExists('question_options');

        // ২. নতুন করে সঠিক কলামসহ টেবিল তৈরি করবে
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->string('option_text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('question_options');
    }
};