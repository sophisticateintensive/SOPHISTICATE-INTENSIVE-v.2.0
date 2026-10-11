<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $existingAdmin = DB::table('users')->where('role', 'admin')->orWhere('email', 'sophisticateintensive@gmail.com')->first();

        if (!$existingAdmin) {
            DB::table('users')->insert([
                'name'              => 'Sophisticate Admin',
                'email'             => 'sophisticateintensive@gmail.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('Sophisticate8746489%'),
                'role'              => 'admin',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }
}
