<?php

namespace App\Policies;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GenrePolicy
{
    public function before(User $user, $ability): ?bool
    {
        if ($user->userRole->role === User::ADMIN_ROLE) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Genre $genre): bool
    {
        return $user->userRole->role === User::MODERATOR_ROLE;
    }

}
