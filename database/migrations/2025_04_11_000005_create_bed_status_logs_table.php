<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bed_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bed_id')->constrained()->onDelete('cascade');
            $table->string('previous_status');
            $table->string('new_status');
            $table->text('housekeeping_remarks')->nullable();
            $table->string('changed_by');
            $table->timestamp('changed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bed_status_logs');
    }
}; 