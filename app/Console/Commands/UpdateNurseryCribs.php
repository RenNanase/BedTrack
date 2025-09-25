<?php

namespace App\Console\Commands;

use App\Models\Bed;
use App\Models\Room;
use App\Models\Ward;
use Illuminate\Console\Command;

class UpdateNurseryCribs extends Command
{
    protected $signature = 'nursery:update-cribs';
    protected $description = 'Update existing nursery cribs to have is_crib set to true';

    public function handle()
    {
        $this->info('Starting to update nursery cribs...');

        // Get the nursery ward
        $nurseryWard = Ward::where('ward_name', 'Nursery Ward')->first();

        if (!$nurseryWard) {
            $this->error('Nursery Ward not found!');
            return 1;
        }

        // Get or create the nursery room
        $nurseryRoom = Room::where('ward_id', $nurseryWard->id)
                            ->where('room_name', 'Nursery Room')
                            ->first();

        if (!$nurseryRoom) {
            $this->info('Creating Nursery Room...');
            $nurseryRoom = Room::create([
                'ward_id' => $nurseryWard->id,
                'room_name' => 'Nursery Room',
                'capacity' => 9, // Default capacity
            ]);

            // Create initial cribs
            for ($i = 1; $i <= 9; $i++) {
                Bed::create([
                    'bed_number' => 'Crib ' . $i,
                    'room_id' => $nurseryRoom->id,
                    'status' => 'Available',
                    'is_crib' => true,
                ]);
            }
            $this->info('Created Nursery Room with 9 cribs.');
        }

        // Update all beds in the nursery room to be cribs
        $updatedCount = Bed::where('room_id', $nurseryRoom->id)
                            ->where('bed_number', 'like', 'Crib%')
                            ->update(['is_crib' => true]);

        $this->info("Successfully updated {$updatedCount} cribs in the nursery room.");
        return 0;
    }
}
