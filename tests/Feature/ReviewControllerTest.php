<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function testCanAddNewReviewByAuthUser(): void
    {
        $userFirst = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $firstResponse = $this->actingAs($userFirst)
            ->postJson('/api/films/5/comments', [
                'text' => 'Something interesting writes here about some film. Great!',
                'rating' => 8
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'text',
                    'rating',
                ],
            ]);

        $content = json_decode($firstResponse->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $newReviewId = $content->data->id;

        $userSecond = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $this->actingAs($userSecond)
            ->postJson('/api/films/5/comments', [
                'text' => 'Something interesting writes here about some film. Great!',
                'comment_id' => $newReviewId
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'text',
                    'review_id',
                ],
            ]);
    }

    public function testCanNotAddNewReviewByGuest(): void
    {
        $this->postJson('/api/films/5/comments', [
                'text' => 'Something interesting writes here about some film. Great!',
                'rating' => 8
            ])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function testCanNotAddNewReviewWithValidationErrorsByAuthUser(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $this->actingAs($user)
            ->postJson('/api/films/5/comments', [
                'text' => 10,
                'rating' => 8
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);

        $this->actingAs($user)
            ->postJson('/api/films/5/comments', [
                'text' => 'Something interesting writes here about some film. Great!',
                'rating' => 'eight'
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);

        $this->actingAs($user)
            ->postJson('/api/films/5/comments', [
                'text' => 'Something interesting writes here about some film. Great!',
                'comment_id' => 100
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);
    }

    public function testCanNotAddNewReviewForNotFoundFilmByAuthUser(): void
    {
        $this->postJson('/api/films/5/comments', [
                'text' => 'Something interesting writes here about some film. Great!',
                'rating' => 8
            ])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure([
                'message',
            ]);
    }

    public function testCanUpdateOwnReviewByAuthUser(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);
        $userId = $user->id;

        $review = Review::factory()
            ->create([
                'film_id' => 1,
                'user_id' => $userId
            ]);
        $reviewId = $review->id;

        $this->actingAs($user)
            ->patchJson('/api/comments/'.$reviewId, [
                'text' => 'Something interesting writes here about some film. Great!',
                'rating' => 8
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'text',
                    'rating',
                ]
            ]);
    }

    public function testCanNotUpdateOtherReviewByAuthUser(): void
    {
        $userFirst = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);
        $userFirstId = $userFirst->id;

        $review = Review::factory()
            ->create([
                'film_id' => 1,
                'user_id' => $userFirstId
            ]);
        $reviewId = $review->id;

        $userSecond = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $this->actingAs($userSecond)
            ->patchJson('/api/comments/'.$reviewId, [
                'text' => 'Something interesting writes here about some film. Great!',
                'rating' => 8
            ])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function testCanUpdateAnyReviewByModerator(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);
        $userId = $user->id;

        $review = Review::factory()
            ->create([
                'film_id' => 1,
                'user_id' => $userId
            ]);
        $reviewId = $review->id;

        $moderator = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::MODERATOR_ROLE)->value('id'),
            ]);

        $this->actingAs($moderator)
            ->patchJson('/api/comments/'.$reviewId, [
                'text' => 'Something interesting writes here about some film. Great!',
                'rating' => 8
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'text',
                    'rating',
                ]
            ]);
    }

    public function testCanNotUpdateNotFoundReview(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $this->actingAs($user)
            ->patchJson('/api/comments/100', [
                'text' => 'Something interesting writes here about some film. Great!',
                'rating' => 8
            ])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function testCanNotUpdateReviewWithValidationErrors(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);
        $userId = $user->id;

        $review = Review::factory()
            ->create([
                'film_id' => 1,
                'user_id' => $userId
            ]);
        $reviewId = $review->id;

        $this->actingAs($user)
            ->patchJson('/api/comments/'.$reviewId, [
                'text' => 34768,
                'rating' => 8
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);

        $this->actingAs($user)
            ->patchJson('/api/comments/'.$reviewId, [
                'text' => 'Something interesting writes here about some film. Great!',
                'rating' => 'sdkjhfskjhfsd'
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);

        $this->actingAs($user)
            ->patchJson('/api/comments/'.$reviewId, [
                'text' => 'Something interesting writes here about some film. Great!',
                'rating' => 8,
                'comment_id' => 150
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);
    }

    public function testCanDeleteOwnReviewByAuthUser(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);
        $userId = $user->id;

        $review = Review::factory()
            ->create([
                'film_id' => 1,
                'user_id' => $userId
            ]);
        $reviewId = $review->id;

        $this->actingAs($user)
            ->deleteJson('/api/comments/'.$reviewId)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data'
            ]);
    }

    public function testCanNotDeleteOtherReviewByAuthUser(): void
    {
        $userFirst = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);
        $userFirstId = $userFirst->id;

        $review = Review::factory()
            ->create([
                'film_id' => 1,
                'user_id' => $userFirstId
            ]);
        $reviewId = $review->id;

        $userSecond = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $this->actingAs($userSecond)
            ->deleteJson('/api/comments/'.$reviewId)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function testCanDeleteAnyReviewByModerator(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);
        $userId = $user->id;

        $review = Review::factory()
            ->create([
                'film_id' => 1,
                'user_id' => $userId
            ]);
        $reviewId = $review->id;

        $moderator = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::MODERATOR_ROLE)->value('id'),
            ]);

        $this->actingAs($moderator)
            ->deleteJson('/api/comments/'.$reviewId)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data'
            ]);
    }

    public function testCanNotDeleteNotFoundReview(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $this->actingAs($user)
            ->deleteJson('/api/comments/100')
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure([
                'message'
            ]);
    }
}
