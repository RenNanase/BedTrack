<?php

namespace App\Console\Commands;

use App\Models\Bed;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoUpdateBedStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'beds:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update beds from Housekeeping to Available after specified time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Starting automatic bed status update...');

            // Find beds that are in Housekeeping status for more than 2 hours
            $beds = Bed::where('status', 'Housekeeping')
                ->whereNotNull('housekeeping_started_at')
                ->where('housekeeping_started_at', '<=', Carbon::now()->subHours(2))
                ->with('room') // Eager load room relationship
                ->get();

            $this->info("Found {$beds->count()} beds to update from Housekeeping to Available.");

            foreach ($beds as $bed) {
                try {
                    $this->info("Updating bed #{$bed->bed_number} in {$bed->room->room_name} to Available.");

                    // Update the bed status
                    $bed->update([
                        'status' => 'Available',
                        'status_changed_at' => Carbon::now(),
                        'housekeeping_started_at' => null,
                        'housekeeping_remarks' => null,
                        'patient_name' => null,
                        'patient_category' => null,
                        'gender' => null,
                        'mrn' => null,
                        'notes' => null,
                    ]);

                    // Log the activity
                    ActivityLogger::log(
                        'Auto-Updated Bed Status',
                        "Automatically changed bed {$bed->bed_number} status from Housekeeping to Available after housekeeping completed.",
                        Bed::class,
                        $bed->id
                    );
                } catch (\Exception $e) {
                    $this->error("Failed to update bed #{$bed->bed_number}: " . $e->getMessage());
                    continue;
                }
            }

            $this->info('Bed status update completed successfully.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('An error occurred during bed status update: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
