<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
    ['email' => 'recchirwa@gmail.com'],
        [
        'name'              => 'Admin',
        'email_verified_at' => now(),
        'password'          => Hash::make('chirwa123'),
        'role'              => 'admin',
        'updated_at'        => now(),
    ]);
    }
}
