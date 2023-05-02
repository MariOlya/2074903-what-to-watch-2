<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\FilmStatus;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PromoControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function testCanNotGetPromoFilmIfItIsNotDefined(): void
    {
        $this->getJson('/api/promo')
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function testCanGetPromoFilm(): void
    {
        $newFilm = Film::factory()->create([
            'status_id' => FilmStatus::whereStatus(Film::FILM_DEFAULT_STATUS)->value('id'),
            'imdb_id' => 'tt1385384',
            'promo' => 1
        ]);

        $response = $this->getJson('/api/promo')
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

        $content = json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $promoId = $content->data->id;

        $this->assertEquals($newFilm->id, $promoId);
    }

    public function testCanGetPromoFilmWithFavoriteStatusByUser(): void
    {
        Film::factory()->create([
            'status_id' => FilmStatus::whereStatus(Film::FILM_DEFAULT_STATUS)->value('id'),
            'imdb_id' => 'tt1385384',
            'promo' => 1
        ]);

        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $response = $this->actingAs($user)
            ->getJson('/api/promo')
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

        $content = json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR);

        $this->assertNotNull($content->data->is_favorite);
        $this->assertEquals(false, $content->data->is_favorite);
    }

    public function testCanSetNewPromoFilmByAdmin(): void
    {
        $promoFilm = Film::factory()->create([
            'status_id' => FilmStatus::whereStatus(Film::FILM_DEFAULT_STATUS)->value('id'),
            'imdb_id' => 'tt1385384',
            'promo' => 1
        ]);
        $promoFilmId = $promoFilm->id;

        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::MODERATOR_ROLE)->value('id'),
            ]);

        $newPromoFilmId = 7;

        $this->actingAs($user)
            ->postJson('/api/promo/'.$newPromoFilmId)
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

        $newPromoFilm = Film::wherePromo(1)->first();
        $previousPromoFilm = Film::whereId($promoFilmId)->first();

        $this->assertNotEquals($promoFilmId, $newPromoFilm->id);
        $this->assertEquals($newPromoFilmId, $newPromoFilm->id);
        $this->assertEquals(0, $previousPromoFilm->promo);
    }

    public function testCanSetNewPromoFilmWithoutPreviousPromoFilmByAdmin(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::MODERATOR_ROLE)->value('id'),
            ]);

        $newPromoFilmId = 7;

        $this->actingAs($user)
            ->postJson('/api/promo/'.$newPromoFilmId)
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

        $newPromoFilm = Film::wherePromo(1)->first();

        $this->assertEquals($newPromoFilmId, $newPromoFilm->id);
    }

    public function testCanNotSetNewPromoFilmByUsualUser(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::ROLE_DEFAULT)->value('id'),
            ]);

        $newPromoFilmId = 7;

        $this->actingAs($user)
            ->postJson('/api/promo/'.$newPromoFilmId)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function testCanNotSetNewPromoFilmByGuest(): void
    {
        $newPromoFilmId = 7;

        $this->postJson('/api/promo/'.$newPromoFilmId)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function testCanNotSetNewPromoNotExistFilm(): void
    {
        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::MODERATOR_ROLE)->value('id'),
            ]);

        $newPromoFilmId = 30;

        $this->actingAs($user)
            ->postJson('/api/promo/'.$newPromoFilmId)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure([
                'message'
            ]);
    }
}
