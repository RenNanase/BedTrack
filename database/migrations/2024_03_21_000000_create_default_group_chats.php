<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Ward;
use App\Models\ChatRoom;
use App\Models\User;

return new class extends Migration
{
    public function up()
    {
        // Get all wards
        $wards = Ward::all();

        foreach ($wards as $ward) {
            // Check if ward already has a group chat
            $existingChat = ChatRoom::where('ward_id', $ward->id)
                ->where('type', 'ward')
                ->first();

            if (!$existingChat) {
                // Create a new group chat for the ward
                $chatRoom = ChatRoom::create([
                    'name' => "{$ward->name} Group Chat",
                    'type' => 'ward',
                    'ward_id' => $ward->id
                ]);

                // Get all users in this ward
                $users = User::where('ward_id', $ward->id)->get();

                // Attach all users to the chat
                $chatRoom->users()->attach($users->pluck('id'));
            }
        }
    }

    public function down()
    {
        // No need to revert this migration
    }
};
