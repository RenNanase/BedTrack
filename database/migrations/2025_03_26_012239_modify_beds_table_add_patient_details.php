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
            // Add new columns
            $table->string('patient_category')->nullable()->after('patient_name');
            $table->string('gender')->nullable()->after('patient_category');
            $table->string('mrn')->nullable()->after('gender');

            // Rename patient_info to notes
            $table->renameColumn('patient_info', 'notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beds', function (Blueprint $table) {
            // Rename notes back to patient_info
            $table->renameColumn('notes', 'patient_info');

            // Drop the new columns
            $table->dropColumn(['patient_category', 'gender', 'mrn']);
        });
    }
};
