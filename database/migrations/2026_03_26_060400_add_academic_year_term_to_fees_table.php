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
        Schema::table('fees', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->after('student_id')->constrained('academic_years')->onDelete('set null');
            $table->foreignId('term_id')->nullable()->after('academic_year_id')->constrained('terms')->onDelete('set null');
            $table->string('description')->nullable()->after('type');
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue'])->default('pending')->after('due_date');
            $table->text('notes')->nullable()->after('status');

            // Add indexes
            $table->index(['student_id', 'academic_year_id', 'term_id']);
            $table->index(['due_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropForeign(['term_id']);
            $table->dropColumn(['academic_year_id', 'term_id', 'description', 'status', 'notes']);
            $table->dropIndex(['student_id', 'academic_year_id', 'term_id']);
            $table->dropIndex(['due_date', 'status']);
        });
    }
};