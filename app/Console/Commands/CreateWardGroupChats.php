<?php

namespace App\Console\Commands;

use App\Models\ChatRoom;
use App\Models\Ward;
use Illuminate\Console\Command;

class CreateWardGroupChats extends Command
{
    protected $signature = 'chat:create-ward-chats';
    protected $description = 'Create group chat rooms for each ward';

    public function handle()
    {
        $this->info('Creating group chat rooms for each ward...');

        $wards = Ward::all();
        $count = 0;

        foreach ($wards as $ward) {
            // Check if group chat already exists
            $existingChat = ChatRoom::where('ward_id', $ward->id)
                ->where('type', 'group')
                ->first();

            if (!$existingChat) {
                // Create new group chat
                $chatRoom = ChatRoom::create([
                    'name' => "{$ward->name} Group Chat",
                    'type' => 'group',
                    'ward_id' => $ward->id,
                ]);

                // Add all ward users to the chat
                $chatRoom->users()->attach($ward->users->pluck('id'));

                $count++;
                $this->info("Created group chat for {$ward->name}");
            }
        }

        $this->info("Completed! Created {$count} new group chat rooms.");
    }
}
