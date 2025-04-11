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
            $table->foreignId('source_bed_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfer_logs', function (Blueprint $table) {
            $table->foreignId('source_bed_id')->nullable(false)->change();
        });
    }
}; 