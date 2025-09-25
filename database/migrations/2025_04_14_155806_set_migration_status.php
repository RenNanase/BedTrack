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
        // Remove problematic migration entries
        DB::table('migrations')->where('migration', '2025_04_14_153257_add_ward_id_to_bassinets_table')->delete();
        DB::table('migrations')->where('migration', '2024_03_21_000000_create_bassinets_table')->delete();
        
        // Add entries for migrations that were already manually applied
        DB::table('migrations')->insert([
            ['migration' => '2024_03_21_000000_create_bassinets_table', 'batch' => 22],
            ['migration' => '2025_04_14_153257_add_ward_id_to_bassinets_table', 'batch' => 22]
        ]);
        
        // Run our new migration
        Schema::table('transfer_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('transfer_logs', 'destination_bassinet_id')) {
                $table->unsignedBigInteger('destination_bassinet_id')->nullable()->after('destination_bed_id');
                $table->foreign('destination_bassinet_id')->references('id')->on('bassinets')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfer_logs', function (Blueprint $table) {
            if (Schema::hasColumn('transfer_logs', 'destination_bassinet_id')) {
                $table->dropForeign(['destination_bassinet_id']);
                $table->dropColumn('destination_bassinet_id');
            }
        });
    }
};
