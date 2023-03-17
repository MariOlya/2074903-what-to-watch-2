<?php

namespace Database\Factories;

use App\Models\FileType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FileType>
 */
class FileTypeFactory extends Factory
{
    protected $model = FileType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fileTypes = [
            'avatar',
            'poster',
            'preview',
            'background'
        ];

        return [
            'type' => $this->faker->unique()->randomElement($fileTypes),
        ];
    }
}
