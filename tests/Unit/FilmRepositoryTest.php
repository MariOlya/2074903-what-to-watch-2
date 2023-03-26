<?php

namespace Tests\Unit;

use App\Models\Film;
use App\Models\FilmStatus;
use App\Repositories\FilmRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class FilmRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic unit test example.
     */
    public function testUpdateFilmRating(): void
    {
        $film = new Film();
        $film->imdb_id = 'tt0465774';
        $film->rating = 6.5;
        $film->vote_amount = 2338;
        $film->status_id = FilmStatus::whereStatus('pending')->value('id');
        $film->save();

        $filmId = $film->id;

        $repository = new FilmRepository();

        /** @var Film $firstUpdate */
        $firstUpdate = $repository->updateRating($filmId, 10);
        /** @var Film $secondUpdate */
        $secondUpdate = $repository->updateRating($filmId, 9);
        /** @var Film $thirdUpdate */
        $thirdUpdate = $repository->updateRating($filmId, 3);

        $this->assertEquals(6.50, $firstUpdate->rating);
        $this->assertEquals(2339, $firstUpdate->vote_amount);

        $this->assertEquals(6.50, $secondUpdate->rating);
        $this->assertEquals(2340, $secondUpdate->vote_amount);

        $this->assertEquals(6.50, $thirdUpdate->rating);
        $this->assertEquals(2341, $thirdUpdate->vote_amount);
    }
}
