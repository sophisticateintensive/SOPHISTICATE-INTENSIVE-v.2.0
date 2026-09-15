<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'recchirwa@gmail.com'],
            [
                'name'     => 'Robert Chirwa',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
            ]
        );

        $this->command->info('Admin user created successfully!');
    }
}
