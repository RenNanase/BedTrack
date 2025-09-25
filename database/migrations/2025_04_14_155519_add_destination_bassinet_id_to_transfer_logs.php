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
            $table->unsignedBigInteger('destination_bassinet_id')->nullable()->after('destination_bed_id');
            $table->foreign('destination_bassinet_id')->references('id')->on('bassinets')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfer_logs', function (Blueprint $table) {
            $table->dropForeign(['destination_bassinet_id']);
            $table->dropColumn('destination_bassinet_id');
        });
    }
};
