<?php

require 'bootstrap/app.php';

$user = \App\Models\User::where('email', 'admin@lucrativa.bet')->first();

if ($user) {
    echo "User exists: " . $user->name . "\n";
    echo "Roles: " . $user->roles->pluck('name')->join(', ') . "\n";
    echo "Has admin role: " . ($user->hasRole('admin') ? 'yes' : 'no') . "\n";
    echo "Has Admin role: " . ($user->hasRole('Admin') ? 'yes' : 'no') . "\n";
} else {
    echo "User not found\n";
}