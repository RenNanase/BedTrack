<?php

// Fix beds status script
// This script should be run with: php database/fix_bed_status.php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Bed;
use Illuminate\Support\Facades\DB;

echo "Checking for beds with status issues...\n";

// First, check if any beds have the wrong status format
$problematicBeds = DB::table('beds')
    ->whereRaw("status NOT IN ('Available', 'Booked', 'Occupied', 'Discharged', 'Housekeeping')")
    ->get();

echo "Found " . count($problematicBeds) . " beds with potential status issues.\n";

foreach ($problematicBeds as $bed) {
    echo "Fixing bed #{$bed->bed_number} (ID: {$bed->id}) - Current status: {$bed->status}\n";

    // Update the status based on what it should be
    DB::table('beds')
        ->where('id', $bed->id)
        ->update(['status' => 'Available']);

    echo "  - Status updated to: Available\n";
}

// Check for potentially corrupted beds with Housekeeping status
$housekeepingBeds = DB::table('beds')
    ->where('status', 'like', '%Housekeeping%')
    ->get();

echo "Found " . count($housekeepingBeds) . " beds with Housekeeping status.\n";

foreach ($housekeepingBeds as $bed) {
    echo "Fixing Housekeeping bed #{$bed->bed_number} (ID: {$bed->id})\n";

    // Ensure the status is properly set
    DB::table('beds')
        ->where('id', $bed->id)
        ->update(['status' => 'Housekeeping']);

    echo "  - Status corrected to: Housekeeping\n";
}

echo "All beds have been checked and fixed if needed.\n";
