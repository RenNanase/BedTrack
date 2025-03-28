<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckSuperAdmin extends Command
{
    protected $signature = 'check:superadmin {email}';
    protected $description = 'Check if a user is superadmin and update if needed';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        $this->info("Current user details:");
        $this->info("ID: {$user->id}");
        $this->info("Name: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->info("Current Role: {$user->role}");

        if ($user->role !== 'superadmin') {
            if ($this->confirm('User is not a superadmin. Would you like to make this user a superadmin?')) {
                $user->role = 'superadmin';
                $user->save();
                $this->info('User has been updated to superadmin role.');
            }
        } else {
            $this->info('User is already a superadmin.');
        }

        return 0;
    }
}
