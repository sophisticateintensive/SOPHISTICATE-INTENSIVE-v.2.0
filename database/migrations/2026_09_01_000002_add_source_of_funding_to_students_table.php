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
        Schema::table('students', function (Blueprint $table) {
            $table->string('source_of_funding')->default('Not Specified')->after('programme');
            $table->string('funding_source_other')->nullable()->after('source_of_funding');
            $table->index('source_of_funding');
        });

        // Set default for existing records
        DB::table('students')
            ->whereNull('source_of_funding')
            ->orWhere('source_of_funding', '')
            ->update(['source_of_funding' => 'Not Specified']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['source_of_funding']);
            $table->dropColumn(['source_of_funding', 'funding_source_other']);
        });
    }
};
