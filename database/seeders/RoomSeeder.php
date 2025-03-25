<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Ward;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // General Ward Rooms
        $generalWard = Ward::where('ward_name', 'General Ward')->first();

        Room::create([
            'room_name' => 'Room 101',
            'ward_id' => $generalWard->id,
            'capacity' => 3,
        ]);

        Room::create([
            'room_name' => 'Room 102',
            'ward_id' => $generalWard->id,
            'capacity' => 3,
        ]);

        Room::create([
            'room_name' => 'Room 103',
            'ward_id' => $generalWard->id,
            'capacity' => 4,
        ]);

        // Cardiac Ward Rooms
        $cardiacWard = Ward::where('ward_name', 'Cardiac Ward')->first();

        Room::create([
            'room_name' => 'Room 201',
            'ward_id' => $cardiacWard->id,
            'capacity' => 4,
        ]);

        Room::create([
            'room_name' => 'Room 202',
            'ward_id' => $cardiacWard->id,
            'capacity' => 4,
        ]);

        // Pediatric Ward Rooms
        $pediatricWard = Ward::where('ward_name', 'Pediatric Ward')->first();

        Room::create([
            'room_name' => 'Room 301',
            'ward_id' => $pediatricWard->id,
            'capacity' => 4,
        ]);

        Room::create([
            'room_name' => 'Room 302',
            'ward_id' => $pediatricWard->id,
            'capacity' => 4,
        ]);

        Room::create([
            'room_name' => 'Room 303',
            'ward_id' => $pediatricWard->id,
            'capacity' => 4,
        ]);

        // Maternity Ward Rooms
        $maternityWard = Ward::where('ward_name', 'Maternity Ward')->first();

        Room::create([
            'room_name' => 'Room 401',
            'ward_id' => $maternityWard->id,
            'capacity' => 3,
        ]);

        Room::create([
            'room_name' => 'Room 402',
            'ward_id' => $maternityWard->id,
            'capacity' => 3,
        ]);
    }
}
