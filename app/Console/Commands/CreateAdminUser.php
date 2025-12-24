<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create 
                            {email? : The admin email address}
                            {password? : The admin password}
                            {--name= : The admin name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user account';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email') ?? $this->ask('Enter admin email');
        $password = $this->argument('password') ?? $this->secret('Enter admin password');
        $name = $this->option('name') ?? $this->ask('Enter admin name', 'Admin');

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            if ($existingUser->role === 'admin') {
                $this->info("User {$email} is already an admin.");
                return Command::SUCCESS;
            }

            // Upgrade existing user to admin
            if ($this->confirm("User {$email} exists. Upgrade to admin?", true)) {
                $existingUser->update(['role' => 'admin']);
                $this->info("User {$email} upgraded to admin successfully!");
                return Command::SUCCESS;
            }

            $this->error('Operation cancelled.');
            return Command::FAILURE;
        }

        // Create new admin user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'status' => 'active',
            'balance' => 0,
        ]);

        $this->info("Admin user created successfully!");
        $this->table(
            ['ID', 'Name', 'Email', 'Role'],
            [[$user->id, $user->name, $user->email, $user->role]]
        );

        return Command::SUCCESS;
    }
}
