<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->foreignId('academic_year_id')
                ->nullable()
                ->after('term_id')
                ->constrained('academic_years')
                ->onDelete('cascade');
        });

        // Backfill academic_year_id from term relationship for any existing results
        DB::statement("
            UPDATE results r
            INNER JOIN terms t ON r.term_id = t.id
            SET r.academic_year_id = t.academic_year_id
            WHERE r.academic_year_id IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn('academic_year_id');
        });
    }
};
