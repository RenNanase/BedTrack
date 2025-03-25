<?php

namespace Database\Seeders;

use App\Models\Ward;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ward::create([
            'ward_name' => 'General Ward',
        ]);

        Ward::create([
            'ward_name' => 'Cardiac Ward',
        ]);

        Ward::create([
            'ward_name' => 'Pediatric Ward',
        ]);

        Ward::create([
            'ward_name' => 'Maternity Ward',
        ]);
    }
}
