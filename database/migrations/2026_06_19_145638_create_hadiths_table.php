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
        Schema::create('hadiths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('hadith_categories')->onDelete('cascade');
            $table->text('arabic_text'); 
            $table->text('bangla_text');
            $table->text('english_text')->nullable(); 
            $table->string('reference'); 
            $table->string('grade')->default('সহীহ'); 
            $table->text('explanation')->nullable(); 
            $table->string('source_url')->nullable(); 
            $table->integer('points')->default(5); 
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hadiths');
    }
};
