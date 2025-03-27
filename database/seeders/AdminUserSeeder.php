<?php

namespace Database\Seeders;

use App\Models\User;
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
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_super_admin' => true,
        ]);

        // Create regular admin user
        User::create([
            'name' => 'admin',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_super_admin' => false,
        ]);

        $this->command->info('Admin users created successfully.');
        $this->command->info('Super Admin: username: superadmin, Password: password');
        $this->command->info('Regular Admin: username: admin, Password: password');
    }
}
