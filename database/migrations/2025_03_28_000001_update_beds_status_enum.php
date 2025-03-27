<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, modify the enum type to include new statuses
        DB::statement("ALTER TABLE beds MODIFY COLUMN status ENUM('Available', 'Booked', 'Occupied', 'Discharged', 'Housekeeping', 'Transfer-in', 'Transfer-out') DEFAULT 'Available'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE beds MODIFY COLUMN status ENUM('Available', 'Booked', 'Occupied', 'Discharged') DEFAULT 'Available'");
    }
};
