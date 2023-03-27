<?php

namespace Tests\Unit;

use App\Models\Film;
use App\Models\Review;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReviewModelTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        (new DatabaseSeeder())->call(TestDatabaseSeeder::class);
    }

    public function testGetDefaultUsernameFromAnonymousReview(): void
    {
        $review = new Review();
        $review->film_id = Film::whereImdbId('tt4005402')->value('id');
        $review->text = 'Something wonderful';
        $review->rating = 8;
        $review->save();

        $anonymousUsername = $review->user->name;

        $this->assertEquals('Гость', $anonymousUsername);
    }
}
