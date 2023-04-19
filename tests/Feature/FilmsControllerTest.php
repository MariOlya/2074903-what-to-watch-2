<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\FilmStatus;
use App\Models\Genre;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FilmsControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function testCanGetFilms(): void
    {
        $this->getJson('/api/films')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'current_page',
                'data' => [[
                    'id',
                    'name',
                    'preview_image' => [],
                    'preview_video_link' => []
                ]],
                'first_page_url',
                'next_page_url',
                'prev_page_url',
                'per_page',
                'total'
            ]);

        $this->getJson('/api/films?page=3')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'current_page',
                'data' => [[
                    'id',
                    'name',
                    'preview_image' => [],
                    'preview_video_link' => []
                ]],
                'first_page_url',
                'next_page_url',
                'prev_page_url',
                'per_page',
                'total'
            ]);

        $this->getJson('/api/films?pageSize=20')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'current_page',
                'data' => [[
                    'id',
                    'name',
                    'preview_image' => [],
                    'preview_video_link' => []
                ]],
                'first_page_url',
                'next_page_url',
                'prev_page_url',
                'per_page',
                'total'
            ]);

        $this->getJson('/api/films?pageSize=20&page=3')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'current_page',
                'data' => [],
                'first_page_url',
                'next_page_url',
                'prev_page_url',
                'per_page',
                'total'
            ]);
    }

    public function testCanGetGenreFilms(): void
    {
        $firstResponse = $this->getJson('/api/films?pageSize=20&genre=adventure')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'current_page',
                'data' => [[
                    'id',
                    'name',
                    'preview_image' => [],
                    'preview_video_link' => []
                ]],
                'first_page_url',
                'next_page_url',
                'prev_page_url',
                'per_page',
                'total'
            ]);

        $firstContent = json_decode($firstResponse->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $databaseCountGenreFilms = count($firstContent->data);

        $newFilm = Film::factory()->create([
            'status_id' => FilmStatus::whereStatus(Film::FILM_DEFAULT_STATUS)->value('id'),
        ]);
        $genreId = Genre::whereGenre('adventure')->value('id');
        $newFilm->genres()->attach($genreId);

        $secondResponse = $this->getJson('/api/films?pageSize=20&genre=adventure')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'current_page',
                'data' => [],
                'first_page_url',
                'next_page_url',
                'prev_page_url',
                'per_page',
                'total'
            ]);

        $secondContent = json_decode($secondResponse->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $databaseNewCountGenreFilms = count($secondContent->data);

        $this->assertEquals($databaseCountGenreFilms + 1, $databaseNewCountGenreFilms);

    }

    public function testCanGetModeratedFilmsByModerator(): void
    {
        $newFilm = Film::factory()->create([
            'status_id' => FilmStatus::whereStatus(Film::MODERATE_FILM_STATUS)->value('id'),
        ]);

        $newFilm->genres()->attach(random_int(1,8));

        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::MODERATOR_ROLE)->value('id'),
            ]);

        $moderatorResponse = $this->actingAs($user)
            ->getJson('/api/films?status=moderate')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'current_page',
                'data' => [[
                    'id',
                    'name',
                    'preview_image' => [],
                    'preview_video_link' => []
                ]],
                'first_page_url',
                'next_page_url',
                'prev_page_url',
                'per_page',
                'total'
            ]);

        $content = json_decode($moderatorResponse->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $databaseModeratedFilms = count($content->data);

        $this->assertEquals(1, $databaseModeratedFilms);
    }

    public function testCanNotGetModeratedFilmsByUsualUser(): void
    {
        $newFilm = Film::factory()->create([
            'status_id' => FilmStatus::whereStatus(Film::MODERATE_FILM_STATUS)->value('id'),
        ]);

        $newFilm->genres()->attach(random_int(1,8));

        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $moderatorResponse = $this->actingAs($user)
            ->getJson('/api/films?pageSize=20&status=moderate')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'current_page',
                'data' => [[
                    'id',
                    'name',
                    'preview_image' => [],
                    'preview_video_link' => []
                ]],
                'first_page_url',
                'next_page_url',
                'prev_page_url',
                'per_page',
                'total'
            ]);

        $content = json_decode($moderatorResponse->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $databaseModeratedFilms = count($content->data);

        $this->assertEquals(20, $databaseModeratedFilms);
    }

    public function testCanGetSortedFilmsByRealisedDesc(): void
    {
        $sortedResponse = $this->getJson('/api/films?order_by=released&order_to=desc&pageSize=20')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'current_page',
                'data' => [[
                    'id',
                    'name',
                    'preview_image' => [],
                    'preview_video_link' => []
                ]],
                'first_page_url',
                'next_page_url',
                'prev_page_url',
                'per_page',
                'total'
            ]);

        $content = json_decode($sortedResponse->getContent(), false, 512, JSON_THROW_ON_ERROR);

        $sortedReleasedYears = array_map(static fn ($film) => $film->released, $content->data);
        $checkedReleasedYears = $sortedReleasedYears;
        rsort($checkedReleasedYears);

        $this->assertEquals($checkedReleasedYears, $sortedReleasedYears);
    }

    public function testCanGetSortedFilmsByRealisedAsc(): void
    {
        $sortedResponse = $this->getJson('/api/films?order_by=released&order_to=asc&pageSize=20')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'current_page',
                'data' => [[
                    'id',
                    'name',
                    'preview_image' => [],
                    'preview_video_link' => []
                ]],
                'first_page_url',
                'next_page_url',
                'prev_page_url',
                'per_page',
                'total'
            ]);

        $content = json_decode($sortedResponse->getContent(), false, 512, JSON_THROW_ON_ERROR);

        $sortedReleasedYears = array_map(static fn ($film) => $film->released, $content->data);
        $checkedReleasedYears = $sortedReleasedYears;
        sort($checkedReleasedYears);

        $this->assertEquals($checkedReleasedYears, $sortedReleasedYears);
    }

    public function testCanGetSortedFilmsByRatingDesc(): void
    {
        $sortedResponse = $this->getJson('/api/films?order_by=rating&order_to=desc&pageSize=20')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'current_page',
                'data' => [[
                    'id',
                    'name',
                    'preview_image' => [],
                    'preview_video_link' => []
                ]],
                'first_page_url',
                'next_page_url',
                'prev_page_url',
                'per_page',
                'total'
            ]);

        $content = json_decode($sortedResponse->getContent(), false, 512, JSON_THROW_ON_ERROR);

        $sortedRatings = array_map(static fn ($film) => $film->rating, $content->data);
        $checkedRatings = $sortedRatings;
        rsort($checkedRatings);

        $this->assertEquals($checkedRatings, $sortedRatings);
    }

    public function testCanGetSortedFilmsByRatingAsc(): void
    {
        $sortedResponse = $this->getJson('/api/films?order_by=rating&order_to=asc&pageSize=20')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'current_page',
                'data' => [[
                    'id',
                    'name',
                    'preview_image' => [],
                    'preview_video_link' => []
                ]],
                'first_page_url',
                'next_page_url',
                'prev_page_url',
                'per_page',
                'total'
            ]);

        $content = json_decode($sortedResponse->getContent(), false, 512, JSON_THROW_ON_ERROR);

        $sortedRatings = array_map(static fn ($film) => $film->rating, $content->data);
        $checkedRatings = $sortedRatings;
        sort($checkedRatings);

        $this->assertEquals($checkedRatings, $sortedRatings);
    }

    public function testCanGetFavoriteFilmsByAuthUser(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::MODERATOR_ROLE)->value('id'),
            ]);

        $user->favoriteFilms()->attach([1,2,3,4,5]);

        $favoriteResponse = $this->actingAs($user)
            ->getJson('/api/favorite')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'current_page',
                'data' => [[
                    'id',
                    'name',
                    'preview_image' => [],
                    'preview_video_link' => []
                ]],
                'first_page_url',
                'next_page_url',
                'prev_page_url',
                'per_page',
                'total'
            ]);

        $content = json_decode($favoriteResponse->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $countFavoriteFilms = count($content->data);

        $this->assertEquals(5, $countFavoriteFilms);
    }

    public function testCanNotGetFavoriteFilmsByNotAuthUser(): void
    {
        $this->getJson('/api/favorite')
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure(['message']);
    }

    public function testCanGetSimilarFilms(): void
    {
        $this->getJson('/api/films/5/similar')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'name',
                    'preview_image' => [],
                    'preview_video_link' => []
                ]],
            ]);
    }

    public function testCanNotGetSimilarFilmsForNotFoundFilm(): void
    {
        $this->getJson('/api/films/22/similar')
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure([
                'message'
            ]);
    }
}
