<?php

namespace App\Console\Commands;

use App\Models\Ward;
use App\Models\Room;
use App\Models\Bed;
use Illuminate\Console\Command;

class FixNurseryWard extends Command
{
    protected $signature = 'nursery:fix';
    protected $description = 'Fix the Nursery Ward setup and ensure it has the correct name and settings';

    public function handle()
    {
        $this->info('Starting to fix Nursery Ward setup...');

        // Get or create the nursery ward
        $nurseryWard = Ward::whereIn('ward_name', ['Nursery Ward', 'Nursery'])->first();

        if (!$nurseryWard) {
            $this->info('Creating Nursery Ward...');
            $nurseryWard = Ward::create([
                'ward_name' => 'Nursery Ward',
                'is_nursery' => true,
                'is_blocked' => false,
            ]);
        } else {
            // Update existing ward to ensure correct name and settings
            $nurseryWard->update([
                'ward_name' => 'Nursery Ward',
                'is_nursery' => true,
                'is_blocked' => false,
            ]);
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
                'capacity' => 9,
                'is_blocked' => false,
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
        } else {
            // Update existing room settings
            $nurseryRoom->update([
                'is_blocked' => false,
            ]);

            // Update all beds in the nursery room to be cribs
            Bed::where('room_id', $nurseryRoom->id)
                ->where('bed_number', 'like', 'Crib%')
                ->update(['is_crib' => true]);
        }

        $this->info('Nursery Ward setup completed successfully.');
        return 0;
    }
} 