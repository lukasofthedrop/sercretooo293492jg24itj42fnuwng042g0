<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ResetUserPassword extends Command
{
    protected $signature = 'user:reset-password {email} {password} {--role=}';
    protected $description = 'Create or update a user with given email and password, optionally assign role(s). Safe for production.';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $password = (string) $this->argument('password');
        $role = (string) ($this->option('role') ?? '');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email.');
            return self::FAILURE;
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => explode('@', $email)[0],
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'status' => 1,
            ]
        );

        // Always set/refresh password on reset
        $user->password = Hash::make($password);
        if (empty($user->email_verified_at)) {
            $user->email_verified_at = now();
        }
        $user->save();

        // Assign roles defensively to satisfy various checks used in codebase
        $rolesToAssign = [];
        if ($role) {
            $map = [
                'admin' => ['admin', 'Admin'],
                'affiliate' => ['afiliado', 'Affiliate'],
                'afiliado' => ['afiliado', 'Affiliate'],
                'player' => ['Player'],
            ];
            $rolesToAssign = $map[strtolower($role)] ?? [$role];
        }

        foreach ($rolesToAssign as $r) {
            $roleModel = Role::firstOrCreate(['name' => $r]);
            if (! $user->hasRole($r)) {
                $user->assignRole($roleModel);
            }
        }

        $this->info("User {$email} updated. Roles: " . implode(',', $rolesToAssign));
        return self::SUCCESS;
    }
}

