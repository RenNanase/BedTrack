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
        Schema::table('bassinets', function (Blueprint $table) {
            $table->unsignedBigInteger('ward_id')->nullable()->after('room_id');
        });

        // Set the ward_id for all existing bassinets
        DB::statement('UPDATE bassinets JOIN rooms ON bassinets.room_id = rooms.id SET bassinets.ward_id = rooms.ward_id');

        // Add foreign key constraint
        Schema::table('bassinets', function (Blueprint $table) {
            $table->foreign('ward_id')->references('id')->on('wards')->cascadeOnDelete();
            $table->nullable(false, 'ward_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bassinets', function (Blueprint $table) {
            $table->dropForeign(['ward_id']);
            $table->dropColumn('ward_id');
        });
    }
};
