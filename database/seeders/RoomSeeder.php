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
        // Maternity Ward Rooms
        $maternityWard = Ward::where('ward_name', 'Maternity Ward')->first();

        Room::create([
            'room_name' => 'Room 101',
            'ward_id' => $maternityWard->id,
            'capacity' => 3,
        ]);

        Room::create([
            'room_name' => 'Room 102',
            'ward_id' => $maternityWard->id,
            'capacity' => 3,
        ]);

        Room::create([
            'room_name' => 'Room 103',
            'ward_id' => $maternityWard->id,
            'capacity' => 4,
        ]);

        // Medical Ward Rooms
        $medicalWard = Ward::where('ward_name', 'Medical Ward')->first();

        Room::create([
            'room_name' => 'Room 201',
            'ward_id' => $medicalWard->id,
            'capacity' => 4,
        ]);

        Room::create([
            'room_name' => 'Room 202',
            'ward_id' => $medicalWard->id,
            'capacity' => 4,
        ]);

        // Multidisciplinary Ward Rooms
        $multidisciplinaryWard = Ward::where('ward_name', 'Multidisciplinary Ward')->first();

        Room::create([
            'room_name' => 'Room 301',
            'ward_id' => $multidisciplinaryWard->id,
            'capacity' => 4,
        ]);

        Room::create([
            'room_name' => 'Room 302',
            'ward_id' => $multidisciplinaryWard->id,
            'capacity' => 4,
        ]);

        Room::create([
            'room_name' => 'Room 303',
            'ward_id' => $multidisciplinaryWard->id,
            'capacity' => 4,
        ]);

        // ICU Rooms
        $icu = Ward::where('ward_name', 'ICU')->first();

        Room::create([
            'room_name' => 'Room 401',
            'ward_id' => $icu->id,
            'capacity' => 3,
        ]);

        Room::create([
            'room_name' => 'Room 402',
            'ward_id' => $icu->id,
            'capacity' => 3,
        ]);

        // Nursery Ward Rooms
        // $nurseryWard = Ward::where('ward_name', 'Nursery Ward')->first();

        // Room::create([
        //     'room_name' => 'Room 501',
        //     'ward_id' => $nurseryWard->id,
        //     'capacity' => 15,
        // ]);
    }
}
