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
        // First change enum to string - this is more flexible
        Schema::table('beds', function (Blueprint $table) {
            // SQLite doesn't support modifying columns directly, so we need a different approach
            // But MySQL can do it with DB::statement
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE beds MODIFY status VARCHAR(50) DEFAULT 'Available'");
            } else {
                // For SQLite or other drivers, we'd need a different approach
                // But since we're on MySQL, this conditional remains for future compatibility
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to enum if needed
        Schema::table('beds', function (Blueprint $table) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE beds MODIFY status ENUM('Available', 'Booked', 'Occupied', 'Discharged') DEFAULT 'Available'");
            }
        });
    }
};
