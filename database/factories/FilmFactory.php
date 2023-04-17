<?php

namespace Database\Factories;

use App\Models\Film;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Film>
 */
class FilmFactory extends Factory
{
    protected $model = Film::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $imdbIds = [
            'tt2262345',
            'tt1196946',
            'tt0207275',
            'tt1270080',
            'tt2582496',
            'tt0392465',
            'tt2872732',
            'tt1385826',
            'tt4005402',
            'tt7858472',
            'tt7713068',
            'tt0060959',
            'tt1311071',
            'tt1277737',
            'tt0133093',
            'tt0084503',
            'tt2011971',
            'tt0086969',
            'tt0116581',
            'tt0120655',
            'tt0144117',
            'tt0144517',
            'tt0142317',
            'tt0874117',
        ];

        return [
            'imdb_id' => $this->faker->unique()->randomElement($imdbIds),
            'name' => $this->faker->unique()->words(3, true),
            'released' => $this->faker->year(),
            'description' => $this->faker->text(1000),
            'run_time' => $this->faker->numberBetween(90, 140),
            'rating' => $this->faker->randomFloat(1, 3, 9),
            'vote_amount' => $this->faker->numberBetween(5, 500),
        ];
    }
}
