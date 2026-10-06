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
        if (Schema::hasTable('academic_years')) {
            Schema::table('academic_years', function (Blueprint $table) {
                if (!Schema::hasColumn('academic_years', 'start_date')) {
                    $table->date('start_date')->nullable()->after('year_name');
                }
                if (!Schema::hasColumn('academic_years', 'end_date')) {
                    $table->date('end_date')->nullable()->after('start_date');
                }
            });
        }

        if (Schema::hasTable('terms')) {
            Schema::table('terms', function (Blueprint $table) {
                if (!Schema::hasColumn('terms', 'start_date')) {
                    $table->date('start_date')->nullable()->after('term_name');
                }
                if (!Schema::hasColumn('terms', 'end_date')) {
                    $table->date('end_date')->nullable()->after('start_date');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('terms')) {
            Schema::table('terms', function (Blueprint $table) {
                if (Schema::hasColumn('terms', 'end_date')) {
                    $table->dropColumn('end_date');
                }
                if (Schema::hasColumn('terms', 'start_date')) {
                    $table->dropColumn('start_date');
                }
            });
        }

        if (Schema::hasTable('academic_years')) {
            Schema::table('academic_years', function (Blueprint $table) {
                if (Schema::hasColumn('academic_years', 'end_date')) {
                    $table->dropColumn('end_date');
                }
                if (Schema::hasColumn('academic_years', 'start_date')) {
                    $table->dropColumn('start_date');
                }
            });
        }
    }
};
