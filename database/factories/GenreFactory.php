<?php

namespace Database\Factories;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Genre>
 */
class GenreFactory extends Factory
{
    protected $model = Genre::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genres = [
            'animation',
            'adventure',
            'drama',
            'crime',
            'mystery',
            'comedy',
            'family',
            'documentary',
            'romance',
        ];

        return [
            'genre' => $this->faker->unique()->randomElement($genres),
        ];
    }
}
