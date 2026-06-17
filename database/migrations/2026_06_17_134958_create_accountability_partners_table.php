<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
Schema::create('accountability_partners', function (Blueprint $table) {
$table->id();
$table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // যে রিকোয়েস্ট পাঠিয়েছে
$table->foreignId('partner_id')->constrained('users')->onDelete('cascade'); // যাকে পাঠানো হয়েছে
$table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
$table->timestamps();

$table->unique(['user_id', 'partner_id']);
});
}

public function down()
{
Schema::dropIfExists('accountability_partners');
}
};