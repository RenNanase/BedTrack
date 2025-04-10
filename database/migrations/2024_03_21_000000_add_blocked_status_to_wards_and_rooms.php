<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add is_blocked column to wards table
        Schema::table('wards', function (Blueprint $table) {
            $table->boolean('is_blocked')->default(false)->after('ward_name');
            $table->text('block_remarks')->nullable()->after('is_blocked');
            $table->timestamp('blocked_at')->nullable()->after('block_remarks');
            $table->foreignId('blocked_by')->nullable()->after('blocked_at')->constrained('users')->onDelete('set null');
        });

        // Add is_blocked column to rooms table if not exists
        if (!Schema::hasColumn('rooms', 'is_blocked')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->boolean('is_blocked')->default(false)->after('room_name');
                $table->text('block_remarks')->nullable()->after('is_blocked');
                $table->timestamp('blocked_at')->nullable()->after('block_remarks');
                $table->foreignId('blocked_by')->nullable()->after('blocked_at')->constrained('users')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        // Remove columns from wards table
        Schema::table('wards', function (Blueprint $table) {
            $table->dropForeign(['blocked_by']);
            $table->dropColumn(['is_blocked', 'block_remarks', 'blocked_at', 'blocked_by']);
        });

        // Remove columns from rooms table
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['blocked_by']);
            $table->dropColumn(['is_blocked', 'block_remarks', 'blocked_at', 'blocked_by']);
        });
    }
}; 