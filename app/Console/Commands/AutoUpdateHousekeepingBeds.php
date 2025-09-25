<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bed;
use App\Models\BedStatusLog;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoUpdateHousekeepingBeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'beds:update-housekeeping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update beds from Housekeeping to Available when cleaning time has elapsed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for beds with completed housekeeping...');
        
        $housekeepingBeds = Bed::where('status', 'Housekeeping')
            ->whereNotNull('housekeeping_started_at')
            ->get();
            
        $updatedCount = 0;
        
        foreach ($housekeepingBeds as $bed) {
            $isTerminalCleaning = str_contains($bed->housekeeping_remarks ?? '', 'Terminal');
            $completionTime = $isTerminalCleaning 
                ? $bed->housekeeping_started_at->addHours(2) 
                : $bed->housekeeping_started_at->addHour();
            
            // If cleaning time has elapsed, automatically update to Available
            if (now()->greaterThan($completionTime)) {
                // Get the previous status for the log
                $oldStatus = $bed->status;
                
                // Update bed status to Available
                $bed->status = 'Available';
                $bed->status_changed_at = now();
                $bed->housekeeping_started_at = null;
                $bed->housekeeping_remarks = null;
                $bed->save();
                
                // Log the automatic status change
                BedStatusLog::create([
                    'bed_id' => $bed->id,
                    'previous_status' => $oldStatus,
                    'new_status' => 'Available',
                    'housekeeping_remarks' => 'Automatically changed after cleaning completion',
                    'changed_by' => 'System',
                    'changed_at' => now(),
                ]);
                
                ActivityLogger::log(
                    'Automatic Status Change',
                    "Bed {$bed->bed_number} in {$bed->room->room_name} automatically changed from Housekeeping to Available after cleaning completion",
                    Bed::class,
                    $bed->id
                );
                
                $this->info("Updated bed {$bed->bed_number} in {$bed->room->room_name} to Available");
                $updatedCount++;
            } else {
                $timeRemaining = $completionTime->diffForHumans(now());
                $this->line("Bed {$bed->bed_number} in {$bed->room->room_name} still in housekeeping (completes $timeRemaining)");
            }
        }
        
        if ($updatedCount > 0) {
            $this->info("Successfully updated $updatedCount beds from Housekeeping to Available");
        } else {
            $this->info("No beds needed to be updated at this time");
        }
        
        // Log to the application log
        Log::info("Auto-update housekeeping beds command completed. Updated $updatedCount beds.");
        
        return 0;
    }
}
