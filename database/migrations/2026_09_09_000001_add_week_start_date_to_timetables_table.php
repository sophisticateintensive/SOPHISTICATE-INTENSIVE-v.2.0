<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('timetables') && !Schema::hasColumn('timetables', 'week_start_date')) {
            Schema::table('timetables', function (Blueprint $table) {
                $table->date('week_start_date')->nullable()->after('term_id')->index();
            });

            // Set existing records to the start of the current week (Monday)
            $currentMonday = now()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString();
            DB::table('timetables')->whereNull('week_start_date')->update([
                'week_start_date' => $currentMonday,
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('timetables') && Schema::hasColumn('timetables', 'week_start_date')) {
            Schema::table('timetables', function (Blueprint $table) {
                $table->dropColumn('week_start_date');
            });
        }
    }
};
