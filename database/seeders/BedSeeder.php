<?php

namespace Database\Seeders;

use App\Models\Bed;
use App\Models\Room;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Safely delete existing beds instead of truncate
        // First disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Bed::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Process rooms by ward for custom naming
        $wards = Ward::all();
        
        foreach ($wards as $ward) {
            $rooms = Room::where('ward_id', $ward->id)->get();
            
            foreach ($rooms as $room) {
                $this->createBedsForRoom($room, $ward);
            }
        }

        // Add some random occupied beds for demo
        $randomBeds = Bed::inRandomOrder()->limit(5)->get();
        foreach ($randomBeds as $bed) {
            $gender = $this->getRandomGender();
            $bed->update([
                'status' => 'Occupied',
                'patient_name' => $this->getRandomName($gender),
                'patient_category' => $this->getRandomCategory(),
                'gender' => $gender,
                'mrn' => $this->getRandomMRN(),
                'notes' => $this->getRandomNotes(),
                'status_changed_at' => Carbon::now()->subHours(rand(1, 48)), // Random time within the past 48 hours
            ]);
        }

        // Add some random booked beds for demo
        $randomBeds = Bed::where('status', 'Available')->inRandomOrder()->limit(3)->get();
        foreach ($randomBeds as $bed) {
            $gender = $this->getRandomGender();
            $bed->update([
                'status' => 'Booked',
                'patient_name' => $this->getRandomName($gender),
                'patient_category' => $this->getRandomCategory(),
                'gender' => $gender,
                'mrn' => $this->getRandomMRN(),
                'notes' => $this->getRandomNotes(),
                'status_changed_at' => Carbon::now()->subHours(rand(1, 24)), // Random time within the past 24 hours
            ]);
        }
    }
    
    /**
     * Create beds for a specific room with custom naming based on ward
     */
    private function createBedsForRoom(Room $room, Ward $ward): void
    {
        $capacity = $room->capacity;
        $wardName = $ward->ward_name;
        
        // Apply standardized naming ONLY to these four specific wards
        if (in_array($wardName, ['ICU', 'Medical Ward', 'Multidisciplinary Ward', 'Maternity Ward'])) {
            $this->createStandardizedBeds($room, $capacity);
        } 
        // For Nursery Ward, create specialized cribs with NUR01-NUR09 naming
        else if ($wardName === 'Nursery Ward') {
            $this->createNurseryBeds($room);
        }
        // Use default naming for other wards
        else {
            $this->createDefaultBeds($room, $capacity);
        }
    }
    
    /**
     * Create beds with standardized naming (SI for single beds, DA/DB for double beds)
     */
    private function createStandardizedBeds(Room $room, int $capacity): void
    {
        if ($capacity == 1) {
            // Single bed rooms use "SI"
            Bed::create([
                'bed_number' => 'SI', //standard
                'room_id' => $room->id,
                'status' => 'Available',
                'status_changed_at' => now(),
            ]);
        } else {
            // Multiple bed rooms use "DA", "DB", etc.
            $bedLetters = ['DA', 'DB', 'DC', 'DD']; //double
            
            for ($i = 0; $i < $capacity && $i < count($bedLetters); $i++) {
                Bed::create([
                    'bed_number' => $bedLetters[$i],
                    'room_id' => $room->id,
                    'status' => 'Available',
                    'status_changed_at' => now(),
                ]);
            }
        }
    }
    
    /**
     * Create nursery beds (cribs) with NUR01-NUR09 naming
     */
    private function createNurseryBeds(Room $room): void
    {
        // Create exactly 9 cribs for the nursery with NUR01-NUR09 naming
        for ($i = 1; $i <= 9; $i++) {
            $bedNumber = 'NUR' . str_pad($i, 2, '0', STR_PAD_LEFT); // Creates NUR01, NUR02, etc.
            
            Bed::create([
                'bed_number' => $bedNumber,
                'room_id' => $room->id,
                'status' => 'Available',
                'status_changed_at' => now(),
                'is_crib' => true,
            ]);
        }
    }
    
    /**
     * Create default beds for other wards
     */
    private function createDefaultBeds(Room $room, int $capacity): void
    {
        for ($i = 1; $i <= $capacity; $i++) {
            $bedNumber = $capacity > 1 ? $room->room_name . "-Bed {$i}" : $room->room_name;
            
            Bed::create([
                'bed_number' => $bedNumber,
                'room_id' => $room->id,
                'status' => 'Available',
                'status_changed_at' => now(),
            ]);
        }
    }

    /**
     * Get a random gender for demo purposes.
     */
    private function getRandomGender(): string
    {
        $genders = ['Male', 'Female'];
        return $genders[array_rand($genders)];
    }

    /**
     * Get a random patient name for demo purposes, based on gender.
     */
    private function getRandomName(string $gender): string
    {
        if ($gender === 'Male') {
            $firstNames = [
                'Haruto',
                'Riku',
                'Souta',
                'Takumi',
                'Ren',
                'Tsubasa',
            ];
        } else {
            $firstNames = [
                'Sakura',
                'Yuki',
                'Hana',
                'Aoi',
                'Rina',
                'Mika'
            ];
        }

        $lastNames = ['Tanaka', 'Takahashi', 'Yamamoto', 'Kobayashi', 'Fujimoto', 'Matsuda', 'Shimizu', 'Ishikawa', 'Nakagawa', 'Kondo', 'Hoshino', 'Sakamoto'];

        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }

    /**
     * Get a random patient category for demo purposes.
     */
    private function getRandomCategory(): string
    {
        $categories = ['Adult', 'Paediatric'];
        return $categories[array_rand($categories)];
    }

    /**
     * Get a random MRN for demo purposes.
     */
    private function getRandomMRN(): string
    {
        return 'MRN' . rand(100000, 999999);
    }

    /**
     * Get random notes for demo purposes.
     */
    private function getRandomNotes(): string
    {
        $diagnoses = ['Fever', 'Hypertension', 'Diabetes', 'Fracture', 'Pneumonia', 'Common Cold', 'Allergic Reaction', 'Migraine'];
        $notes = [
            'Patient requires regular monitoring',
            'Medication administered at 8AM daily',
            'Allergic to penicillin',
            'Family has been notified',
            'Awaiting test results',
            'Patient requests vegetarian meals',
            'Follow-up appointment scheduled'
        ];

        $patientInfo = "Diagnosis: " . $diagnoses[array_rand($diagnoses)] . "\n";
        $patientInfo .= "Notes: " . $notes[array_rand($notes)];

        return $patientInfo;
    }
}

