<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\FilmStatus;
use App\Models\Review;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FilmControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function testCanGetFilmInfo(): void
    {
        $this->getJson('/api/films/5')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'poster_image' => [],
                    'preview_image' => [],
                    'background_image' => [],
                    'background_color' => [],
                    'video_link' => [],
                    'preview_video_link' => [],
                    'description',
                    'rating',
                    'vote_amount',
                    'director' => [],
                    'actors' => [],
                    'run_time',
                    'genres' => [],
                    'released',
                ],
            ]);
    }

    public function testCanNotGetInfoForNotFoundFilm(): void
    {
        $this->getJson('/api/films/25')
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function testCanGetIsFavoriteStatusInFilmInfoForAuthUser(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $this->actingAs($user)
            ->getJson('/api/films/5')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'poster_image' => [],
                    'preview_image' => [],
                    'background_image' => [],
                    'background_color' => [],
                    'video_link' => [],
                    'preview_video_link' => [],
                    'description',
                    'rating',
                    'vote_amount',
                    'director' => [],
                    'actors' => [],
                    'run_time',
                    'genres' => [],
                    'released',
                    'is_favorite'
                ],
            ]);
    }

    public function testCanAddNewFavoriteFilmForAuthUser(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $this->actingAs($user)
            ->postJson('/api/films/5/favorite')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'poster_image' => [],
                    'preview_image' => [],
                    'background_image' => [],
                    'background_color' => [],
                    'video_link' => [],
                    'preview_video_link' => [],
                    'description',
                    'rating',
                    'vote_amount',
                    'director' => [],
                    'actors' => [],
                    'run_time',
                    'genres' => [],
                    'released',
                ],
            ]);
    }

    public function testCanNotAddNewFavoriteFilmForGuest(): void
    {
        $this->postJson('/api/films/5/favorite')
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function testCanNotAddTheSameFilmAsFavoriteForAuthUser(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $user->favoriteFilms()->attach(5);

        $this->actingAs($user)
            ->postJson('/api/films/5/favorite')
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function testCanNotAddNotFoundFilmAsFavoriteForAuthUser(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $this->actingAs($user)
            ->postJson('/api/films/25/favorite')
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function testCanDeleteFavoriteFilmForAuthUser(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $user->favoriteFilms()->attach(5);

        $this->actingAs($user)
            ->deleteJson('/api/films/5/favorite')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data'
            ]);
    }

    public function testCanNotDeleteFavoriteFilmForGuest(): void
    {
        $this->deleteJson('/api/films/5/favorite')
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function testCanNotDeleteNotFoundFavoriteFilmForAuthUser(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $this->actingAs($user)
            ->deleteJson('/api/films/25/favorite')
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function testCanNotDeleteTheSameFavoriteFilmForAuthUser(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $this->actingAs($user)
            ->deleteJson('/api/films/5/favorite')
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function testCanAddNewFilmByModerator(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::MODERATOR_ROLE)->value('id'),
            ]);

        $this->actingAs($user)
            ->postJson('/api/films', ['imdb_id' => 'tt1385384'])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'imdb_id'
                ]
            ]);
    }

    public function testCanNotAddNewFilmWithValidationErrorsByModerator(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::MODERATOR_ROLE)->value('id'),
            ]);

        $this->actingAs($user)
            ->postJson('/api/films', ['imdb_id' => 't1385384'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);
    }

    public function testCanNotAddNewFilmByUsualUser(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $this->actingAs($user)
            ->postJson('/api/films', ['imdb_id' => 'tt1385384'])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function testCanUpdateFilmByModerator(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::MODERATOR_ROLE)->value('id'),
            ]);

        $newFilm = Film::factory()->create([
            'status_id' => FilmStatus::whereStatus(Film::NEW_FILM_STATUS)->value('id'),
            'imdb_id' => 'tt1385384'
        ]);

        $newFilmId = $newFilm->id;

        $this->actingAs($user)
            ->patchJson('/api/films/'.$newFilmId, [
                'imdb_id' => 'tt1385384',
                'name' => 'Some new film',
                'status' => Film::FILM_DEFAULT_STATUS,
                'background_color' => '#57gy84',
                'video_link' => 'https://youtu.be/gB2fjsAFPIU',
                'preview_video_link' => 'https://youtu.be/RnYZNDKzOSQ',
                'description' => 'This film you should watch Sunday evening because it is with your favorite actor and really pleasant',
                'director' => 'Bill Potter',
                'starring' => ['Harry Potter', 'Petr First', 'Patrik Pattinson'],
                'genre' => ['adventure', 'drama'],
                'run_time' => 54,
                'released' => 2003,
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                        'id',
                        'imdb_id',
                        'name',
                        'poster_image' => [],
                        'preview_image' => [],
                        'background_image' => [],
                        'background_color' => [],
                        'video_link' => [],
                        'preview_video_link' => [],
                        'description',
                        'rating',
                        'vote_amount',
                        'director' => [],
                        'actors' => [],
                        'run_time',
                        'genres' => [],
                        'released',
                        'status_id'
                    ]
            ]);
    }

    public function testCanNotUpdateFilmWithValidationErrorsByModerator(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::MODERATOR_ROLE)->value('id'),
            ]);

        $newFilm = Film::factory()->create([
            'status_id' => FilmStatus::whereStatus(Film::NEW_FILM_STATUS)->value('id'),
            'imdb_id' => 'tt1385384'
        ]);

        $newFilmId = $newFilm->id;

        $this->actingAs($user)
            ->patchJson('/api/films/'.$newFilmId, [
                'imdb_id' => 't1385384',
                'name' => 'Some new film',
                'status' => Film::FILM_DEFAULT_STATUS,
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);

        $this->actingAs($user)
            ->patchJson('/api/films/'.$newFilmId, [
                'imdb_id' => 'tt1385384',
                'name' => 56,
                'status' => Film::FILM_DEFAULT_STATUS,
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);

        $this->actingAs($user)
            ->patchJson('/api/films/'.$newFilmId, [
                'imdb_id' => 'tt1385384',
                'name' => 'Some new film',
                'status' => 'new',
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);

        $this->actingAs($user)
            ->patchJson('/api/films/'.$newFilmId, [
                'imdb_id' => 'tt1385384',
                'name' => 'Some new film',
                'status' => Film::FILM_DEFAULT_STATUS,
                'poster_image' => 'some-image'
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);

        $this->actingAs($user)
            ->patchJson('/api/films/'.$newFilmId, [
                'imdb_id' => 'tt1385384',
                'name' => 'Some new film',
                'status' => Film::FILM_DEFAULT_STATUS,
                'preview_image' => 'file/some-image.png'
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);

        $this->actingAs($user)
            ->patchJson('/api/films/'.$newFilmId, [
                'imdb_id' => 'tt1385384',
                'name' => 'Some new film',
                'status' => Film::FILM_DEFAULT_STATUS,
                'background_image' => 'img/some-image'
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);

        $this->actingAs($user)
            ->patchJson('/api/films/'.$newFilmId, [
                'imdb_id' => 'tt1385384',
                'name' => 'Some new film',
                'status' => Film::FILM_DEFAULT_STATUS,
                'background_color' => '#uio987e'
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);

        $this->actingAs($user)
            ->patchJson('/api/films/'.$newFilmId, [
                'imdb_id' => 'tt1385384',
                'name' => 'Some new film',
                'status' => Film::FILM_DEFAULT_STATUS,
                'preview_video_link' => '//youtu.be/RnYZNDKzOSQ'
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);

        $this->actingAs($user)
            ->patchJson('/api/films/'.$newFilmId, [
                'imdb_id' => 'tt1385384',
                'name' => 'Some new film',
                'status' => Film::FILM_DEFAULT_STATUS,
                'video_link' => 'youtu.be/RnYZNDKzOSQ'
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);

        $this->actingAs($user)
            ->patchJson('/api/films/'.$newFilmId, [
                'imdb_id' => 'tt1385384',
                'name' => 'Some new film',
                'status' => Film::FILM_DEFAULT_STATUS,
                'description' => 44287
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);

        $this->actingAs($user)
            ->patchJson('/api/films/'.$newFilmId, [
                'imdb_id' => 'tt1385384',
                'name' => 'Some new film',
                'status' => Film::FILM_DEFAULT_STATUS,
                'director' => 'F'
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);

        $this->actingAs($user)
            ->patchJson('/api/films/'.$newFilmId, [
                'imdb_id' => 'tt1385384',
                'name' => 'Some new film',
                'status' => Film::FILM_DEFAULT_STATUS,
                'starring' => 'Bill True'
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);

        $this->actingAs($user)
            ->patchJson('/api/films/'.$newFilmId, [
                'imdb_id' => 'tt1385384',
                'name' => 'Some new film',
                'status' => Film::FILM_DEFAULT_STATUS,
                'genre' => 'drama'
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);

        $this->actingAs($user)
            ->patchJson('/api/films/'.$newFilmId, [
                'imdb_id' => 'tt1385384',
                'name' => 'Some new film',
                'status' => Film::FILM_DEFAULT_STATUS,
                'run_time' => 'something'
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);

        $this->actingAs($user)
            ->patchJson('/api/films/'.$newFilmId, [
                'imdb_id' => 'tt1385384',
                'name' => 'Some new film',
                'status' => Film::FILM_DEFAULT_STATUS,
                'released' => 1756
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => []
            ]);
    }

    public function testCanNotUpdateFilmByUsualUser(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $newFilm = Film::factory()->create([
            'status_id' => FilmStatus::whereStatus(Film::NEW_FILM_STATUS)->value('id'),
            'imdb_id' => 'tt1385384'
        ]);

        $newFilmId = $newFilm->id;

        $this->actingAs($user)
            ->patchJson('/api/films/'.$newFilmId, [
                'imdb_id' => 'tt1385384',
                'name' => 'Some new film',
                'status' => Film::FILM_DEFAULT_STATUS,
                'background_color' => '#57gy84',
                'video_link' => 'https://youtu.be/gB2fjsAFPIU',
                'preview_video_link' => 'https://youtu.be/RnYZNDKzOSQ',
                'description' => 'This film you should watch Sunday evening because it is with your favorite actor and really pleasant',
                'director' => 'Bill Potter',
                'starring' => ['Harry Potter', 'Petr First', 'Patrik Pattinson'],
                'genre' => ['adventure', 'drama'],
                'run_time' => 54,
                'released' => 2003,
            ])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function testCanNotUpdateNotFoundFilmByModerator(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::MODERATOR_ROLE)->value('id'),
            ]);

        $this->actingAs($user)
            ->patchJson('/api/films/25', [
                'imdb_id' => 'tt1385384',
                'name' => 'Some new film',
                'status' => Film::FILM_DEFAULT_STATUS,
                'background_color' => '#57gy84',
                'video_link' => 'https://youtu.be/gB2fjsAFPIU',
                'preview_video_link' => 'https://youtu.be/RnYZNDKzOSQ',
                'description' => 'This film you should watch Sunday evening because it is with your favorite actor and really pleasant',
                'director' => 'Bill Potter',
                'starring' => ['Harry Potter', 'Petr First', 'Patrik Pattinson'],
                'genre' => ['adventure', 'drama'],
                'run_time' => 54,
                'released' => 2003,
            ])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function testCanGetReviewsForFilm(): void
    {
        Review::factory()->create([
            'film_id' => Film::whereId(5)->value('id'),
        ]);

        $this->getJson('/api/films/5/comments')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'text',
                    'created_at',
                    'rating',
                    'user' => [],
                ]],
            ]);
    }

    public function testCanNotGetReviewsForNotFoundFilm(): void
    {
        $this->getJson('/api/films/25/comments')
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure([
                'message'
            ]);
    }
}
