<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'sophisticateintensive@gmail.com'],
            [
                'name'              => 'Sophisticate Admin',
                'email'             => 'sophisticateintensive@gmail.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('nafex05@'),
                'role'              => 'admin',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        );
    }
}
