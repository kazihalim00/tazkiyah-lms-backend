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
        Schema::table('partner_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('reply_to_id')->nullable()->after('message');
            $table->string('reaction')->nullable()->after('reply_to_id');
            $table->foreign('reply_to_id')->references('id')->on('partner_messages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_messages', function (Blueprint $table) {
            //
        });
    }
};
