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
        Schema::table('students', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('programme');
            $table->string('parent_name')->nullable()->after('phone');
            $table->string('parent_phone')->nullable()->after('parent_name');
            $table->string('address')->nullable()->after('parent_phone');
            $table->string('emergency_contact')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['phone', 'parent_name', 'parent_phone', 'address', 'emergency_contact']);
        });
    }
};
