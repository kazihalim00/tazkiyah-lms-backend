<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ibadah_trackers', function (Blueprint $table) {
            // Check and add columns ONLY if they don't exist
            if (!Schema::hasColumn('ibadah_trackers', 'khushu_level')) {
                $table->integer('khushu_level')->default(5);
            }
            if (!Schema::hasColumn('ibadah_trackers', 'evening_adhkar')) {
                $table->boolean('evening_adhkar')->default(false);
            }
            if (!Schema::hasColumn('ibadah_trackers', 'tahajjud')) {
                $table->boolean('tahajjud')->default(false);
            }
            if (!Schema::hasColumn('ibadah_trackers', 'witr')) {
                $table->boolean('witr')->default(false);
            }
            if (!Schema::hasColumn('ibadah_trackers', 'sadaqah')) {
                $table->boolean('sadaqah')->default(false);
            }
            if (!Schema::hasColumn('ibadah_trackers', 'quran_pages')) {
                $table->integer('quran_pages')->default(0);
            }
            if (!Schema::hasColumn('ibadah_trackers', 'journal_notes')) {
                $table->text('journal_notes')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('ibadah_trackers', function (Blueprint $table) {
            // Drop columns safely
            $columnsToDrop = [];

            $fields = ['khushu_level', 'evening_adhkar', 'tahajjud', 'witr', 'sadaqah', 'quran_pages', 'journal_notes'];

            foreach ($fields as $field) {
                if (Schema::hasColumn('ibadah_trackers', $field)) {
                    $columnsToDrop[] = $field;
                }
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};