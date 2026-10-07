<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    protected $signature   = 'admin:reset-password {password? : The new password}';
    protected $description = 'Reset the admin user password';

    public function handle(): int
    {
        $password = $this->argument('password')
            ?? $this->secret('Enter new admin password');

        if (empty($password)) {
            $this->error('Password cannot be empty.');
            return self::FAILURE;
        }

        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->error('No admin user found.');
            return self::FAILURE;
        }

        $admin->update(['password' => $password]);

        $this->info("✅ Password updated for admin: {$admin->email}");

        return self::SUCCESS;
    }
}
