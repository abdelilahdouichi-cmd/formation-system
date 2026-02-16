<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    protected $signature = 'admin:setup {--name= : Admin name} {--email= : Admin email} {--phone= : Admin phone} {--password= : Admin password}';

    protected $description = 'Create or update the initial super admin user';

    public function handle(): int
    {
        $this->info('Setting up Super Admin user...');

        $name = $this->option('name') ?? config('app.admin_name') ?? $this->ask('Admin name');
        $email = $this->option('email') ?? config('app.admin_email') ?? $this->ask('Admin email');
        $phone = $this->option('phone') ?? config('app.admin_phone') ?? $this->ask('Admin phone (optional)', '');
        $password = $this->option('password') ?? config('app.admin_password') ?? $this->secret('Admin password');

        if (empty($password)) {
            $password = $this->secret('Admin password');
        }

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
        ], [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error("  - $error");
            }

            return self::FAILURE;
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'phone' => $phone !== '' ? $phone : null,
                'password' => Hash::make($password),
                'role' => UserRole::SuperAdmin,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        if ($user->wasRecentlyCreated) {
            $this->info("✓ Super Admin user created successfully!");
            $this->line("  Email: $email");
            $this->line("  Name: $name");
            $this->line("  Role: Super Admin");
        } else {
            $this->info("✓ Super Admin user already exists");
            $this->line("  Email: {$user->email}");
            $this->line("  Name: {$user->name}");
            $this->line("  Role: {$user->role->label()}");

            if ($this->confirm('Do you want to update the password?')) {
                $user->update(['password' => Hash::make($password)]);
                $this->info("✓ Password updated successfully");
            }
        }

        return self::SUCCESS;
    }
}
