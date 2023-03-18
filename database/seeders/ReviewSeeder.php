<?php

namespace Database\Seeders;

use App\Models\Film;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 40; $i++)
        {
            Review::factory()
                ->count(1)
                ->for(User::factory()->state([
                    'id' => random_int(1, 10)
                ]))
                ->for(Film::factory()->state([
                    'id' => random_int(1, 20)
                ]))
                ->create();
        }
    }
}
