<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Add academic_year_id column
            $table->foreignId('academic_year_id')
                ->nullable()
                ->after('student_id')
                ->constrained('academic_years')
                ->onDelete('set null');

            // Add term_id column
            $table->foreignId('term_id')
                ->nullable()
                ->after('academic_year_id')
                ->constrained('terms')
                ->onDelete('set null');

            // Add indexes for better performance
            $table->index(['student_id', 'academic_year_id', 'term_id']);
            $table->index(['academic_year_id', 'term_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['academic_year_id']);
            $table->dropForeign(['term_id']);

            // Drop columns
            $table->dropColumn(['academic_year_id', 'term_id']);

            // Drop indexes
            $table->dropIndex(['student_id', 'academic_year_id', 'term_id']);
            $table->dropIndex(['academic_year_id', 'term_id']);
        });
    }
};