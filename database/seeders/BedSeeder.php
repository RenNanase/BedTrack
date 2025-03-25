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
            $bed->update([
                'status' => 'Occupied',
                'patient_name' => $this->getRandomName(),
                'patient_info' => $this->getRandomPatientInfo(),
                'status_changed_at' => Carbon::now()->subHours(rand(1, 48)), // Random time within the past 48 hours
            ]);
        }

        // Add some random booked beds for demo
        $randomBeds = Bed::where('status', 'Available')->inRandomOrder()->limit(3)->get();
        foreach ($randomBeds as $bed) {
            $bed->update([
                'status' => 'Booked',
                'patient_name' => $this->getRandomName(),
                'patient_info' => $this->getRandomPatientInfo(),
                'status_changed_at' => Carbon::now()->subHours(rand(1, 24)), // Random time within the past 24 hours
            ]);
        }

        // Add some discharged beds for demo
        $randomBeds = Bed::where('status', 'Available')->inRandomOrder()->limit(2)->get();
        foreach ($randomBeds as $bed) {
            $bed->update([
                'status' => 'Discharged',
                'patient_name' => $this->getRandomName(),
                'patient_info' => $this->getRandomPatientInfo(),
                'status_changed_at' => Carbon::now()->subHours(rand(1, 12)), // Random time within the past 12 hours
            ]);
        }
    }

    /**
     * Get a random patient name for demo purposes.
     */
    private function getRandomName(): string
    {
        $firstNames = ['James', 'John', 'Robert', 'Michael', 'William', 'David', 'Mary', 'Patricia', 'Jennifer', 'Linda', 'Elizabeth', 'Susan'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Jones', 'Brown', 'Davis', 'Miller', 'Wilson', 'Moore', 'Taylor', 'Anderson', 'Thomas'];

        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }

    /**
     * Get random patient information for demo purposes.
     */
    private function getRandomPatientInfo(): string
    {
        $patientIds = ['PT-' . rand(10000, 99999)];
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

        $patientInfo = "Patient ID: " . $patientIds[array_rand($patientIds)] . "\n";
        $patientInfo .= "Diagnosis: " . $diagnoses[array_rand($diagnoses)] . "\n";
        $patientInfo .= "Notes: " . $notes[array_rand($notes)];

        return $patientInfo;
    }
}
