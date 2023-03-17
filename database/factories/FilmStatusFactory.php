<?php

namespace Database\Factories;

use App\Models\FilmStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FilmStatus>
 */
class FilmStatusFactory extends Factory
{
    protected $model = FilmStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filmStatuses = [
            'pending',
            'moderate',
            'ready'
        ];

        return [
            'status' => $this->faker->unique()->randomElement($filmStatuses),
        ];
    }
}
