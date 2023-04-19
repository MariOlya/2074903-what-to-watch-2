<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
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
    public function update(User $user, Review $review): bool
    {
        if ($user->userRole->role === User::MODERATOR_ROLE) {
            return true;
        }

        return $user->id === $review->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Review $review): bool
    {
        if ($user->userRole->role === User::MODERATOR_ROLE) {
            return true;
        }

        return $user->id === $review->user_id;
    }
}
