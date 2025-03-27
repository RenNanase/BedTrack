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
            $table->boolean('has_hazard')->default(false)->after('notes');
            $table->text('hazard_notes')->nullable()->after('has_hazard');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beds', function (Blueprint $table) {
            $table->dropColumn(['has_hazard', 'hazard_notes']);
        });
    }
};
