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
        Schema::table('wards', function (Blueprint $table) {
            $table->boolean('is_nursery')->default(false)->after('ward_name');
        });

        // Update existing Nursery Ward
        DB::table('wards')
            ->where('ward_name', 'Nursery Ward')
            ->update(['is_nursery' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wards', function (Blueprint $table) {
            $table->dropColumn('is_nursery');
        });
    }
}; 