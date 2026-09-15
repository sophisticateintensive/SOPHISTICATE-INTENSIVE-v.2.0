<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_student_enrollments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('term_id')->constrained()->onDelete('cascade');
            $table->string('programme');
            $table->enum('status', ['active', 'graduated', 'suspended', 'withdrawn'])->default('active');
            $table->date('enrollment_date')->default(now());
            $table->date('expected_graduation_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Ensure a student can't be enrolled twice in same term/year
            $table->unique(['student_id', 'academic_year_id', 'term_id'], 'unique_student_enrollment');
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_enrollments');
    }
};