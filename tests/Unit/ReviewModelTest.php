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
        $this->artisan('db:seed');
    }

    public function testGetDefaultUsernameFromAnonymousReview(): void
    {
        $review = Review::factory()->create([
            'film_id' => 1
        ]);

        $anonymousUsername = $review->user->name;

        $this->assertEquals('Гость', $anonymousUsername);
    }
}
