<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quizzes', function (Blueprint $table) {
            if (!Schema::hasColumn('quizzes', 'academic_year_id')) {
                $table->foreignId('academic_year_id')->nullable()->after('subject_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('quizzes', 'term_id')) {
                $table->foreignId('term_id')->nullable()->after('academic_year_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('quizzes', 'max_attempts')) {
                $table->integer('max_attempts')->default(1)->after('passing_score');
            }
            if (!Schema::hasColumn('quizzes', 'shuffle_questions')) {
                $table->boolean('shuffle_questions')->default(false)->after('max_attempts');
            }
            if (!Schema::hasColumn('quizzes', 'shuffle_options')) {
                $table->boolean('shuffle_options')->default(false)->after('shuffle_questions');
            }
            if (!Schema::hasColumn('quizzes', 'show_correct_answers')) {
                $table->boolean('show_correct_answers')->default(true)->after('shuffle_options');
            }
            if (!Schema::hasColumn('quizzes', 'available_from')) {
                $table->dateTime('available_from')->nullable()->after('time_limit_minutes');
            }
            if (!Schema::hasColumn('quizzes', 'available_until')) {
                $table->dateTime('available_until')->nullable()->after('available_from');
            }
        });

        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'type')) {
                $table->string('type')->default('multiple_choice')->after('quiz_id'); // multiple_choice, true_false
            }
            if (!Schema::hasColumn('questions', 'explanation')) {
                $table->text('explanation')->nullable()->after('question_text');
            }
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('quiz_attempts', 'time_taken_seconds')) {
                $table->integer('time_taken_seconds')->default(0)->after('submitted_at');
            }
        });
    }

    public function down()
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropForeign(['term_id']);
            $table->dropColumn([
                'academic_year_id',
                'term_id',
                'max_attempts',
                'shuffle_questions',
                'shuffle_options',
                'show_correct_answers',
                'available_from',
                'available_until'
            ]);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['type', 'explanation']);
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropColumn(['time_taken_seconds']);
        });
    }
};
