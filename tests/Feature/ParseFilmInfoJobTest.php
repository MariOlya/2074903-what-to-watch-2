<?php

namespace Tests\Feature;

use App\Factories\Interfaces\FilmFileFactoryInterface;
use App\Factories\Interfaces\LinkFactoryInterface;
use App\Jobs\ParseFilmInfoJob;
use App\Models\File;
use App\Models\Film;
use App\Models\FilmStatus;
use App\Models\Link;
use App\Models\User;
use App\Models\UserRole;
use App\Repositories\HtmlAcademyFilmApiRepository;
use App\Repositories\Interfaces\FilmRepositoryInterface;
use App\Repositories\OmdbFilmApiRepository;
use App\Services\FileService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Queue;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ParseFilmInfoJobTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function testCanUpdateInfoByJob(): void
    {
        Queue::fake();

        $user = User::factory()
            ->create([
                'user_role_id' => UserRole::whereRole(User::MODERATOR_ROLE)->value('id'),
            ]);

        $response = $this->actingAs($user)
            ->postJson('/api/films', ['imdb_id' => 'tt1385384'])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'imdb_id'
                ]
            ]);

        Queue::assertPushed(ParseFilmInfoJob::class);

        $response->assertStatus(201);
    }

    /**
     * @throws \JsonException|\PHPUnit\Framework\MockObject\Exception
     */
    public function testAddParsedInfoFromExternalApi(): void
    {
        $linkFactory = App::makeWith(LinkFactoryInterface::class, ['link' => new Link()]);
        $filmImageFactory = App::makeWith(FilmFileFactoryInterface::class, [
            'file' => new File(),
            'service' => new FileService()
        ]);

        $imdb = 'tt0111161';

        Film::factory()->create([
            'status_id' => FilmStatus::whereStatus(Film::NEW_FILM_STATUS)->value('id'),
            'imdb_id' => $imdb
        ]);

        $newFilm = Film::whereImdbId($imdb)->first();

        $omdbRepository = $this->createMock(OmdbFilmApiRepository::class);
        $omdbRepository
            ->expects($this->exactly(2))
            ->method('getMovieInfoById')->with($imdb)
            ->willReturn(
                json_decode(
                    file_get_contents(base_path('tests/Fixtures/omdb-response-fake.json')),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                )
            );

        $htmlAcademyRepository = $this->createMock(HtmlAcademyFilmApiRepository::class);
        $htmlAcademyRepository
            ->expects($this->exactly(2))
            ->method('getMovieInfoById')->with($imdb)
            ->willReturn(
                json_decode(
                    file_get_contents(base_path('tests/Fixtures/html-academy-response-fake.json')),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                )
            );

        $filmRepository = App::makeWith(FilmRepositoryInterface::class, [
            'imageFactory' => $filmImageFactory,
            'linkFactory' => $linkFactory
        ]);

        (new ParseFilmInfoJob($imdb))->handle($omdbRepository, $htmlAcademyRepository, $filmRepository);

        $updatedFilm = Film::whereImdbId($imdb)->first();

        $omdbResponse = $omdbRepository->getMovieInfoById($imdb);
        $htmlAcademyResponse = $htmlAcademyRepository->getMovieInfoById($imdb);

        $this->assertIsArray($omdbResponse);
        $this->assertIsArray($htmlAcademyResponse);
        $this->assertNull($newFilm->poster_image_id);
        $this->assertEquals(1, $updatedFilm->poster_image_id);
        $this->assertNull($newFilm->video_link_id);
        $this->assertEquals(1, $updatedFilm->video_link_id);

        $this->assertEquals($omdbResponse['data']['Title'], $updatedFilm->name);
        $this->assertEquals($htmlAcademyResponse['data']['video'], $updatedFilm->videoLink->link);
    }
}
