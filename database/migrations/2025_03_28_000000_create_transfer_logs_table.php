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
        Schema::create('transfer_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_bed_id')->constrained('beds')->onDelete('cascade');
            $table->foreignId('destination_bed_id')->constrained('beds')->onDelete('cascade');
            $table->foreignId('source_room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('destination_room_id')->constrained('rooms')->onDelete('cascade');
            $table->string('patient_name');
            $table->string('patient_category');
            $table->string('gender');
            $table->string('mrn');
            $table->text('notes')->nullable();
            $table->timestamp('transferred_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_logs');
    }
};
