<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin user
        User::create([
            'name' => 'superadmin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'superadmin'
        ]);

        // Create regular admin user
        User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        $this->command->info('Admin users created successfully.');
        $this->command->info('Super Admin:');
        $this->command->info('  Email: superadmin@example.com');
        $this->command->info('  Password: password123');
        $this->command->info('Regular Admin:');
        $this->command->info('  Email: admin@example.com');
        $this->command->info('  Password: password');
    }
}
