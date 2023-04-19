<?php

namespace App\Policies;

use App\Models\Film;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FilmPolicy
{
    public function before(User $user, $ability): ?bool
    {
        if ($user->userRole->role === User::ADMIN_ROLE) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->userRole->role === User::MODERATOR_ROLE;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Film $film): bool
    {
        return $user->userRole->role === User::MODERATOR_ROLE;
    }
}
