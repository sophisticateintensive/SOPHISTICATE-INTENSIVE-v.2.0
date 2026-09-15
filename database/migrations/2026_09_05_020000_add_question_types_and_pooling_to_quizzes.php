<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            if (!Schema::hasColumn('quizzes', 'questions_per_attempt')) {
                $table->integer('questions_per_attempt')->nullable()->after('max_attempts')
                    ->comment('Number of questions randomly pooled per attempt (Question Bank)');
            }
        });

        Schema::table('quiz_answers', function (Blueprint $table) {
            if (!Schema::hasColumn('quiz_answers', 'text_answer')) {
                $table->text('text_answer')->nullable()->after('selected_option_id')
                    ->comment('Student free-text response for short essay / text questions');
            }
            if (!Schema::hasColumn('quiz_answers', 'is_graded')) {
                $table->boolean('is_graded')->default(true)->after('is_correct')
                    ->comment('False if essay/short answer requires lecturer manual grading');
            }
            if (!Schema::hasColumn('quiz_answers', 'grader_feedback')) {
                $table->text('grader_feedback')->nullable()->after('points_earned')
                    ->comment('Lecturer feedback/remarks during manual grading');
            }
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('quiz_attempts', 'needs_manual_grading')) {
                $table->boolean('needs_manual_grading')->default(false)->after('is_passed')
                    ->comment('True if attempt contains un-graded essay questions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            if (Schema::hasColumn('quizzes', 'questions_per_attempt')) {
                $table->dropColumn('questions_per_attempt');
            }
        });

        Schema::table('quiz_answers', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('quiz_answers', 'text_answer')) $cols[] = 'text_answer';
            if (Schema::hasColumn('quiz_answers', 'is_graded')) $cols[] = 'is_graded';
            if (Schema::hasColumn('quiz_answers', 'grader_feedback')) $cols[] = 'grader_feedback';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('quiz_attempts', 'needs_manual_grading')) {
                $table->dropColumn('needs_manual_grading');
            }
        });
    }
};
