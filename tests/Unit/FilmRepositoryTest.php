<?php

namespace Tests\Unit;

use App\Factories\FilmImageFactory;
use App\Factories\LinkFactory;
use App\Models\Film;
use App\Models\FilmStatus;
use App\Repositories\FilmRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class FilmRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testUpdateFilmRating(): void
    {
        $film = new Film;
        $film->imdb_id = 'tt0465774';
        $film->rating = 6.5;
        $film->vote_amount = 2338;
        $film->status_id = FilmStatus::whereStatus('pending')->value('id');
        $film->save();

        $filmId = $film->id;
        $filmUpdated = $film->updated_at;

        $filmFileFactory = $this->createMock(FilmImageFactory::class);
        $linkFactory = $this->createMock(LinkFactory::class);

        $repository = new FilmRepository($filmFileFactory, $linkFactory);

        /** @var Film $firstUpdate */
        $firstUpdate = $repository->updateRating($filmId, 10);
        /** @var Film $secondUpdate */
        $secondUpdate = $repository->updateRating($filmId, 9);
        /** @var Film $thirdUpdate */
        $thirdUpdate = $repository->updateRating($filmId, 3);

        $this->assertEquals(6.50, $firstUpdate->rating);
        $this->assertEquals(2339, $firstUpdate->vote_amount);
        $this->assertEquals($filmUpdated, $firstUpdate->updated_at);

        $this->assertEquals(6.50, $secondUpdate->rating);
        $this->assertEquals(2340, $secondUpdate->vote_amount);
        $this->assertEquals($filmUpdated, $secondUpdate->updated_at);

        $this->assertEquals(6.50, $thirdUpdate->rating);
        $this->assertEquals(2341, $thirdUpdate->vote_amount);
        $this->assertEquals($filmUpdated, $thirdUpdate->updated_at);
    }
}
