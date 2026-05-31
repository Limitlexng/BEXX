<?php

namespace App\Policies;

use App\Models\Motorcycle;
use App\Models\User;

class MotorcyclePolicy
{
    public function view(User $user, Motorcycle $motorcycle): bool
    {
        return $user->isAdmin() || $user->partner?->id === $motorcycle->partner_id;
    }

    public function update(User $user, Motorcycle $motorcycle): bool
    {
        return $user->isAdmin() || $user->partner?->id === $motorcycle->partner_id;
    }

    public function delete(User $user, Motorcycle $motorcycle): bool
    {
        return $user->isAdmin() || $user->partner?->id === $motorcycle->partner_id;
    }
}
