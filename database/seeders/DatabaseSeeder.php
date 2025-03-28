<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First create wards
        $this->call([
            WardSeeder::class,
        ]);

        // Then create rooms
        $this->call([
            RoomSeeder::class,
        ]);

        // Then create beds
        $this->call([
            BedSeeder::class,
        ]);

        // Then create admin users
        $this->call([
            AdminUserSeeder::class,
        ]);

        // Finally create staff users
        $this->call([
            UserSeeder::class,
        ]);
    }
}
