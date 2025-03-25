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
        // Create admin user
        User::create([
            'name' => 'admin',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        $this->command->info('Admin user created successfully. Username: admin, Password: password');
    }
}
