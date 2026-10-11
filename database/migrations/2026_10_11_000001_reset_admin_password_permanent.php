<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $hashed = Hash::make('Sophisticate8746489%');

        // Update all admin accounts or the specific admin email
        DB::table('users')
            ->where('role', 'admin')
            ->orWhere('email', 'sophisticateintensive@gmail.com')
            ->update([
                'password'   => $hashed,
                'is_active'  => true,
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
