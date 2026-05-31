<?php

namespace App\Policies;

use App\Models\Rider;
use App\Models\User;

class RiderPolicy
{
    public function view(User $user, Rider $rider): bool
    {
        return $user->isAdmin() || $user->partner?->id === $rider->partner_id;
    }

    public function update(User $user, Rider $rider): bool
    {
        return $user->isAdmin() || $user->partner?->id === $rider->partner_id;
    }

    public function delete(User $user, Rider $rider): bool
    {
        return $user->isAdmin() || $user->partner?->id === $rider->partner_id;
    }
}
