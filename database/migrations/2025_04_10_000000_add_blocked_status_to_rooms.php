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
        Schema::table('rooms', function (Blueprint $table) {
            $table->boolean('is_blocked')->default(false)->after('room_type');
            $table->text('block_remarks')->nullable()->after('is_blocked');
            $table->timestamp('blocked_at')->nullable()->after('block_remarks');
            $table->foreignId('blocked_by')->nullable()->after('blocked_at')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['is_blocked', 'block_remarks', 'blocked_at', 'blocked_by']);
        });
    }
}; 