<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testCanGetGenres():void
    {
        Genre::factory()->count(8)->create();

        $this->getJson('/api/genres')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'genres' => [['id', 'genre']]
                ]
            ]);
    }

    public function testCanUpdateGenreByModerator(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::MODERATOR_ROLE)->value('id'),
            ]);

        $genre = Genre::factory()->create();
        $newGenre = 'new';

        $genreId = $genre->id;

        $this->actingAs($user)
            ->patchJson('/api/genres/'.$genreId, ['genre' => $newGenre])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'updatedGenre' => ['id', 'genre']
                ]
            ]);
    }

    public function testCanNotUpdateGenreWithoutAuth(): void
    {
        $genre = Genre::factory()->create();
        $newGenre = 'new';

        $genreId = $genre->id;

        $this->patchJson('/api/genres/'.$genreId, ['genre' => $newGenre])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure(['message']);
    }

    public function testCanNotUpdateGenreWithNotModeratorRole(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $genre = Genre::factory()->create();
        $newGenre = 'new';

        $genreId = $genre->id;

        $this->actingAs($user)
            ->patchJson('/api/genres/'.$genreId, ['genre' => $newGenre])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure(['message']);
    }
}
