<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ChatRoom;
use App\Models\User;

return new class extends Migration
{
    public function up()
    {
        // Check if global chat already exists
        $globalChat = ChatRoom::where('type', 'global')->first();

        if (!$globalChat) {
            // Create the global chat
            $globalChat = ChatRoom::create([
                'name' => 'Global Chat',
                'type' => 'global',
                'ward_id' => null // Global chat doesn't belong to any ward
            ]);

            // Get all users and attach them to the global chat
            $users = User::all();
            $globalChat->users()->attach($users->pluck('id'));
        }
    }

    public function down()
    {
        // No need to revert this migration
    }
};
