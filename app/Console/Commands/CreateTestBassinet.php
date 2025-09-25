<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateTestBassinet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-test-bassinet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test bassinet for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find a room in a non-nursery ward
        $room = \App\Models\Room::whereHas('ward', function($query) {
            $query->where('is_nursery', false);
        })->first();

        if (!$room) {
            $this->error('No room found in a maternity ward (non-nursery ward)');
            return 1;
        }

        // Create a bassinet for that room
        $bassinet = new \App\Models\Bassinet();
        $bassinet->bassinet_number = 'B' . rand(100, 999);
        $bassinet->room_id = $room->id;
        $bassinet->ward_id = $room->ward_id;
        $bassinet->status = 'Available';
        $bassinet->save();

        $this->info("Created bassinet {$bassinet->bassinet_number} in room {$room->room_name} of ward {$room->ward->ward_name}");
        
        // Create additional bassinets with different statuses
        $statuses = ['Occupied', 'Transfer-in', 'Transfer-out'];
        
        foreach ($statuses as $status) {
            $bassinet = new \App\Models\Bassinet();
            $bassinet->bassinet_number = 'B' . rand(100, 999);
            $bassinet->room_id = $room->id;
            $bassinet->ward_id = $room->ward_id;
            $bassinet->status = $status;
            
            if ($status === 'Occupied') {
                $bassinet->patient_name = 'Baby Test';
                $bassinet->gender = rand(0, 1) ? 'Male' : 'Female';
                $bassinet->mrn = 'MRN' . rand(10000, 99999);
                $bassinet->mother_name = 'Mother Test';
                $bassinet->mother_mrn = 'MRN' . rand(10000, 99999);
                $bassinet->occupied_at = now();
            }
            
            $bassinet->save();
            
            $this->info("Created {$status} bassinet {$bassinet->bassinet_number} in room {$room->room_name}");
        }
        
        return 0;
    }
}
