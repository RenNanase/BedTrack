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
            $table->boolean('had_hazard')->default(false)->after('notes');
            $table->boolean('maintained_hazard')->default(false)->after('had_hazard');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfer_logs', function (Blueprint $table) {
            $table->dropColumn('had_hazard');
            $table->dropColumn('maintained_hazard');
        });
    }
};
