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
            'room_name' => 'MS617',
            'ward_id' => $maternityWard->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => 'MS618',
            'ward_id' => $maternityWard->id,
            'capacity' => 1,//Single
        ]);

        Room::create([
            'room_name' => 'MS621',
            'ward_id' => $maternityWard->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => 'MS623',
            'ward_id' => $maternityWard->id,
            'capacity' => 1,
        ]);

        Room::create([
            'room_name' => 'MS625',
            'ward_id' => $maternityWard->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => 'MS701',
            'ward_id' => $maternityWard->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => 'MS703',
            'ward_id' => $maternityWard->id,
            'capacity' => 1, //Single
        ]);

        // Medical Ward Rooms
        $medicalWard = Ward::where('ward_name', 'Medical Ward')->first();

        Room::create([
            'room_name' => '501 (ISO)',
            'ward_id' => $medicalWard->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => '509',
            'ward_id' => $medicalWard->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => '510',
            'ward_id' => $medicalWard->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => '511',
            'ward_id' => $medicalWard->id,
            'capacity' => 2, // A & B
        ]);

        Room::create([
            'room_name' => '512',
            'ward_id' => $medicalWard->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => '513',
            'ward_id' => $medicalWard->id,
            'capacity' => 2, // A & B
        ]);

        Room::create([
            'room_name' => '516',
            'ward_id' => $medicalWard->id,
            'capacity' => 2, // A & B
        ]);

        Room::create([
            'room_name' => '517',
            'ward_id' => $medicalWard->id,
            'capacity' => 2, // A & B
        ]);

        Room::create([
            'room_name' => '518',
            'ward_id' => $medicalWard->id,
            'capacity' => 2, // A & B
        ]);

        Room::create([
            'room_name' => '519',
            'ward_id' => $medicalWard->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => '520',
            'ward_id' => $medicalWard->id,
            'capacity' => 2, // A & B
        ]);

        Room::create([
            'room_name' => '620',
            'ward_id' => $medicalWard->id,
            'capacity' => 1, //Single
        ]);

        // Multidisciplinary Ward Rooms
        $multidisciplinaryWard = Ward::where('ward_name', 'Multidisciplinary Ward')->first();

        Room::create([
            'room_name' => '601',
            'ward_id' => $multidisciplinaryWard->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => '602',
            'ward_id' => $multidisciplinaryWard->id,
            'capacity' => 2, // A & B
        ]);

        Room::create([
            'room_name' => '603',
            'ward_id' => $multidisciplinaryWard->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => '604',
            'ward_id' => $multidisciplinaryWard->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => '605',
            'ward_id' => $multidisciplinaryWard->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => '606',
            'ward_id' => $multidisciplinaryWard->id,
            'capacity' => 2, // A & B
        ]);

        Room::create([
            'room_name' => '607',
            'ward_id' => $multidisciplinaryWard->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => '608',
            'ward_id' => $multidisciplinaryWard->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => '609',
            'ward_id' => $multidisciplinaryWard->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => '610',
            'ward_id' => $multidisciplinaryWard->id,
            'capacity' => 2, // A & B
        ]);

        Room::create([
            'room_name' => '611',
            'ward_id' => $multidisciplinaryWard->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => '614',
            'ward_id' => $multidisciplinaryWard->id,
            'capacity' => 2, // A & B
        ]);

        Room::create([
            'room_name' => '615',
            'ward_id' => $multidisciplinaryWard->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => '620',
            'ward_id' => $multidisciplinaryWard->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => '621',
            'ward_id' => $multidisciplinaryWard->id,
            'capacity' => 1, //Single
        ]);

        // ICU Rooms
        $icu = Ward::where('ward_name', 'ICU')->first();

        Room::create([
            'room_name' => '702',
            'ward_id' => $icu->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => '701',
            'ward_id' => $icu->id,
            'capacity' => 1, //Single
        ]);


        Room::create([
            'room_name' => '703',
            'ward_id' => $icu->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => '705 ',
            'ward_id' => $icu->id,
            'capacity' => 1, 
        ]);

        Room::create([
            'room_name' => '706 (NICU)',
            'ward_id' => $icu->id,
            'capacity' => 1, //Single
        ]);

        Room::create([
            'room_name' => '707 (ISO)',
            'ward_id' => $icu->id,
            'capacity' => 1, //Single
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
