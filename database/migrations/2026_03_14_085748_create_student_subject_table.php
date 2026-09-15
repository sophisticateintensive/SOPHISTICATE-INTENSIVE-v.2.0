<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('term_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['enrolled', 'completed', 'dropped'])->default('enrolled');
            $table->date('enrolled_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Prevent duplicate assignments
            $table->unique(['student_id', 'subject_id', 'academic_year_id', 'term_id'], 'student_subject_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_subject');
    }
};