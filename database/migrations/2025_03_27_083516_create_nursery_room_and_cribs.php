<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Ward;
use App\Models\Room;
use App\Models\Bed;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the nursery ward (assuming it was just created)
        $nurseryWard = Ward::where('ward_name', 'Nursery')->first();

        if ($nurseryWard) {
            // Create a room for the nursery
            $room = Room::create([
                'room_name' => 'Nursery Room',
                'ward_id' => $nurseryWard->id,
                'capacity' => 20, // Increased capacity for 20 cribs
            ]);

            // Create cribs for the nursery room
            for ($i = 1; $i <= 20; $i++) {
                Bed::create([
                    'bed_number' => 'Crib ' . $i,
                    'room_id' => $room->id,
                    'status' => 'Available',
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get the nursery ward
        $nurseryWard = Ward::where('ward_name', 'Nursery')->first();

        if ($nurseryWard) {
            // Find the nursery room
            $nurseryRoom = Room::where('ward_id', $nurseryWard->id)
                                ->where('room_name', 'Nursery Room')
                                ->first();

            if ($nurseryRoom) {
                // Delete all cribs associated with this room
                Bed::where('room_id', $nurseryRoom->id)->delete();

                // Delete the nursery room
                $nurseryRoom->delete();
            }
        }
    }
};
