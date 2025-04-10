<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('beds', function (Blueprint $table) {
            // Only add the column if it doesn't exist yet
            if (!Schema::hasColumn('beds', 'bed_type')) {
                $table->string('bed_type')->default('regular')->after('bed_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beds', function (Blueprint $table) {
            if (Schema::hasColumn('beds', 'bed_type')) {
                $table->dropColumn('bed_type');
            }
        });
    }
};
