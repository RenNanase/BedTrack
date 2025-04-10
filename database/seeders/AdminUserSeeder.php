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
            'password' => Hash::make('superadmin'),
            'role' => 'superadmin',
        ]);

        // Create regular admin user
        User::create([
            'name' => 'admin',
            'password' => Hash::make('admin'),
            'role' => 'admin',
        ]);

        $this->command->info('Admin users created:');
        $this->command->info('Super Admin:');
        $this->command->info('  Username: superadmin');
        $this->command->info('  Password: superadmin');
        $this->command->line('');
        $this->command->info('Admin:');
        $this->command->info('  Username: admin');
        $this->command->info('  Password: admin');
    }
}
