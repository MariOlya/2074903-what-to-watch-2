<?php

namespace Database\Factories;

use App\Models\LinkType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LinkType>
 */
class LinkTypeFactory extends Factory
{
    protected $model = LinkType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $linkTypes = [
            'video',
            'preview'
        ];

        return [
            'type' => $this->faker->unique()->randomElement($linkTypes),
        ];
    }
}
