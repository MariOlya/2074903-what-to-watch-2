<?php

namespace Tests\Feature;

use App\Factories\Interfaces\ReviewFactoryInterface;
use App\Jobs\ParseCommentsForNewFilmJob;
use App\Models\Film;
use App\Models\FilmStatus;
use App\Models\Review;
use App\Models\User;
use App\Models\UserRole;
use App\Repositories\CommentsApiRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Queue;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ParseCommentsForNewFilmJobTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

   public function testCanParseCommentsForNewFilm(): void
   {
       Queue::fake();

       $user = User::factory()
           ->create([
               'user_role_id' => UserRole::whereRole(User::MODERATOR_ROLE)->value('id'),
           ]);

       $response = $this->actingAs($user)
           ->postJson('/api/films', ['imdb_id' => 'tt0382932'])
           ->assertStatus(Response::HTTP_CREATED)
           ->assertJsonStructure([
               'data' => [
                   'id',
                   'imdb_id'
               ]
           ]);

       Queue::assertPushed(ParseCommentsForNewFilmJob::class);

       $response->assertStatus(201);
   }

    /**
     * @throws \JsonException|\PHPUnit\Framework\MockObject\Exception
     * @throws \Exception
     */
    public function testCanAddParsedCommentsFromExternalApi(): void
    {
        $imdb = 'tt0382932';

        Film::factory()->create([
            'status_id' => FilmStatus::whereStatus(Film::NEW_FILM_STATUS)->value('id'),
            'imdb_id' => $imdb
        ]);

        $commentApiRepository = $this->createMock(CommentsApiRepository::class);
        $commentApiRepository
            ->expects($this->exactly(2))
            ->method('getCommentsByFilmImdbId')->with($imdb)
            ->willReturn(
                json_decode(
                    file_get_contents(base_path('tests/Fixtures/comments-for-new-film-fake.json')),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                )
            );

        (new ParseCommentsForNewFilmJob($imdb))->handle($commentApiRepository);

        $film = Film::whereImdbId($imdb)->first();

        $commentsApiResponse = $commentApiRepository->getCommentsByFilmImdbId($imdb);
        $filmNewComments = $film->reviews()->get();

        $this->assertIsArray($commentsApiResponse);
        $this->assertNotEmpty($filmNewComments);
        $this->assertCount(4, $filmNewComments);
    }

    /**
     * @throws \JsonException|\PHPUnit\Framework\MockObject\Exception
     * @throws \Exception
     */
    public function testCanNotAddCommentsWithoutRatingFromExternalApi(): void
    {
        $imdb = 'tt0382932';

        Film::factory()->create([
            'status_id' => FilmStatus::whereStatus(Film::NEW_FILM_STATUS)->value('id'),
            'imdb_id' => $imdb
        ]);

        $commentApiRepository = $this->createMock(CommentsApiRepository::class);
        $commentApiRepository
            ->expects($this->exactly(2))
            ->method('getCommentsByFilmImdbId')->with($imdb)
            ->willReturn(
                json_decode(
                    file_get_contents(base_path('tests/Fixtures/comments-for-new-film-fake-without-rating.json')),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                )
            );

        (new ParseCommentsForNewFilmJob($imdb))->handle($commentApiRepository);

        $film = Film::whereImdbId($imdb)->first();

        $commentsApiResponse = $commentApiRepository->getCommentsByFilmImdbId($imdb);
        $filmNewComments = $film->reviews()->get();

        $this->assertIsArray($commentsApiResponse);
        $this->assertEmpty($filmNewComments);
        $this->assertCount(0, $filmNewComments);
    }
}
