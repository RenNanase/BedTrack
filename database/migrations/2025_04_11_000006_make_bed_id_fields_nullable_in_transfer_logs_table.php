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
        Schema::table('transfer_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('source_bed_id')->nullable()->change();
            $table->unsignedBigInteger('destination_bed_id')->nullable()->change();
            $table->unsignedBigInteger('source_room_id')->nullable()->change();
            $table->unsignedBigInteger('destination_room_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfer_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('source_bed_id')->nullable(false)->change();
            $table->unsignedBigInteger('destination_bed_id')->nullable(false)->change();
            $table->unsignedBigInteger('source_room_id')->nullable(false)->change();
            $table->unsignedBigInteger('destination_room_id')->nullable(false)->change();
        });
    }
}; 