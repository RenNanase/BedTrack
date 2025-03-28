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
        // Get or create wards
        $maternityWard = Ward::firstOrCreate(['ward_name' => 'Maternity Ward']);
        $nurseryWard = Ward::firstOrCreate(['ward_name' => 'Nursery Ward']);
        $icuWard = Ward::firstOrCreate(['ward_name' => 'ICU Ward']);
        $mdWard = Ward::firstOrCreate(['ward_name' => 'MD Ward']);
        $medicalWard = Ward::firstOrCreate(['ward_name' => 'Medical Ward']);

        // Create maternity staff
        $maternityStaff = User::create([
            'name' => 'maternitystaff',
            'email' => 'maternitystaff@gmail.com',
            'password' => Hash::make('password'),
            'ward_id' => $maternityWard->id,
            'role' => 'staff'
        ]);
        $maternityStaff->wards()->attach($maternityWard->id);

        // Create nursery staff
        $nurseryStaff = User::create([
            'name' => 'nurserystaff',
            'email' => 'nurserystaff@gmail.com',
            'password' => Hash::make('password'),
            'ward_id' => $nurseryWard->id,
            'role' => 'staff'
        ]);
        $nurseryStaff->wards()->attach($nurseryWard->id);

        // Create ICU staff
        $icuStaff = User::create([
            'name' => 'icustaff',
            'email' => 'icustaff@gmail.com',
            'password' => Hash::make('password'),
            'ward_id' => $icuWard->id,
            'role' => 'staff'
        ]);
        $icuStaff->wards()->attach($icuWard->id);

        // Create MD staff
        $mdStaff = User::create([
            'name' => 'mdstaff',
            'email' => 'mdstaff@gmail.com',
            'password' => Hash::make('password'),
            'ward_id' => $mdWard->id,
            'role' => 'staff'
        ]);
        $mdStaff->wards()->attach($mdWard->id);

        // Create medical staff
        $medicalStaff = User::create([
            'name' => 'medicalstaff',
            'email' => 'medicalstaff@gmail.com',
            'password' => Hash::make('password'),
            'ward_id' => $medicalWard->id,
            'role' => 'staff'
        ]);
        $medicalStaff->wards()->attach($medicalWard->id);

        $this->command->info('Staff users created successfully.');
        $this->command->info('All users password: password');
    }
}
