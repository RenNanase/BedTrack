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
        Schema::create('bassinets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('bassinet_number');
            $table->string('status')->default('Available'); // Available, Occupied, Transfer-out, Transfer-in
            $table->string('patient_name')->nullable();
            $table->string('mrn')->nullable();
            $table->string('gender')->nullable();
            $table->string('patient_category')->default('Paediatric');
            $table->boolean('has_hazard')->default(false);
            $table->text('hazard_notes')->nullable();
            $table->timestamp('status_changed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bassinets');
    }
};
