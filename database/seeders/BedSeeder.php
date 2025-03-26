<?php

namespace Database\Seeders;

use App\Models\Bed;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::all();

        foreach ($rooms as $room) {
            for ($i = 1; $i <= $room->capacity; $i++) {
                Bed::create([
                    'bed_number' => 'Bed ' . $i,
                    'room_id' => $room->id,
                    'status' => 'Available',
                ]);
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

        // Add some discharged beds for demo
        $randomBeds = Bed::where('status', 'Available')->inRandomOrder()->limit(2)->get();
        foreach ($randomBeds as $bed) {
            $gender = $this->getRandomGender();
            $bed->update([
                'status' => 'Discharged',
                'patient_name' => $this->getRandomName($gender),
                'patient_category' => $this->getRandomCategory(),
                'gender' => $gender,
                'mrn' => $this->getRandomMRN(),
                'notes' => $this->getRandomNotes(),
                'status_changed_at' => Carbon::now()->subHours(rand(1, 12)), // Random time within the past 12 hours
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
