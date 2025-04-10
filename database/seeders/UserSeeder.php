<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ward;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create staff users for each ward
        $wards = Ward::all();
        foreach ($wards as $ward) {
            $user = User::create([
                'name' => strtolower(str_replace(' ', '', $ward->ward_name)),
                'password' => Hash::make('password'),
                'role' => 'staff',
                'ward_id' => $ward->id,
            ]);

            // Assign user to the ward
            $user->wards()->attach($ward->id);
        }

        $this->command->info('Staff users created successfully.');
        $this->command->info('All users password: password');
    }
}
