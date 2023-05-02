<?php

namespace Tests\Feature;

use App\Jobs\ParseFilmInfoJob;
use App\Jobs\ParseNewCommentsJob;
use App\Models\Film;
use App\Models\FilmStatus;
use App\Models\User;
use App\Models\UserRole;
use App\Repositories\CommentsApiRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ParseNewCommentsJobTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function testCanParseNewCommentsByJob(): void
    {
        Queue::fake();

        app()->call('App\Http\Controllers\ReviewController@parseCommentsExternalApi');

        Queue::assertPushed(ParseNewCommentsJob::class);
    }

    /**
     * @throws \JsonException|\PHPUnit\Framework\MockObject\Exception
     * @throws \Exception
     */
    public function testCanNotAddParsedCommentsWithoutRatingFromExternalApi(): void
    {
        $imdbIds = ['tt0382932', 'tt0382933', 'tt0382934'];

        foreach ($imdbIds as $imdbId) {
            Film::factory()->create([
                'status_id' => FilmStatus::whereStatus(Film::NEW_FILM_STATUS)->value('id'),
                'imdb_id' => $imdbId
            ]);
        }

        $commentApiRepository = $this->createMock(CommentsApiRepository::class);
        $commentApiRepository
            ->expects($this->exactly(2))
            ->method('getAllNewComments')
            ->willReturn(
                json_decode(
                    file_get_contents(base_path('tests/Fixtures/new-comments-per-day-fake-without-rating.json')),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                )
            );

        (new ParseNewCommentsJob())->handle($commentApiRepository);

        $commentsApiResponse = $commentApiRepository->getAllNewComments();
        $this->assertIsArray($commentsApiResponse);

        foreach ($imdbIds as $imdbId) {
            $film = Film::whereImdbId($imdbId)->first();

            if($film) {
                $filmNewComments = $film->reviews()->get();

                $this->assertEmpty($filmNewComments);
                $this->assertCount(0, $filmNewComments);
            }
        }
    }

    /**
     * @throws \JsonException|\PHPUnit\Framework\MockObject\Exception
     * @throws \Exception
     */
    public function testCanAddParsedCommentsFromExternalApi(): void
    {
        $imdbIds = ['tt0382932', 'tt0382933', 'tt0382934'];

        foreach ($imdbIds as $imdbId) {
            Film::factory()->create([
                'status_id' => FilmStatus::whereStatus(Film::NEW_FILM_STATUS)->value('id'),
                'imdb_id' => $imdbId
            ]);
        }

        $commentApiRepository = $this->createMock(CommentsApiRepository::class);
        $commentApiRepository
            ->expects($this->exactly(2))
            ->method('getAllNewComments')
            ->willReturn(
                json_decode(
                    file_get_contents(base_path('tests/Fixtures/new-comments-per-day-fake.json')),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                )
            );

        (new ParseNewCommentsJob())->handle($commentApiRepository);

        $commentsApiResponse = $commentApiRepository->getAllNewComments();
        $this->assertIsArray($commentsApiResponse);

        foreach ($imdbIds as $imdbId) {
            $film = Film::whereImdbId($imdbId)->first();

            if($film) {
                $filmNewComments = $film->reviews()->get();

                $this->assertNotEmpty($filmNewComments);
                $this->assertCount(1, $filmNewComments);
            }
        }
    }
}
