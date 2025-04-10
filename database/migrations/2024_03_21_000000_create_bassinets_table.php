<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bassinets', function (Blueprint $table) {
            $table->id();
            $table->string('bassinet_number');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('ward_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('Available'); // Available, Occupied, Transfer-out, Transfer-in
            $table->string('patient_name')->nullable();
            $table->string('mrn')->nullable();
            $table->string('gender')->nullable();
            $table->string('patient_category')->nullable();
            $table->boolean('has_hazard')->default(false);
            $table->text('hazard_notes')->nullable();
            $table->timestamp('status_changed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bassinets');
    }
}; 