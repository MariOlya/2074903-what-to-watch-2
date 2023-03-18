<?php

namespace Database\Seeders;

use App\Models\Actor;
use App\Models\Director;
use App\Models\Film;
use App\Models\FilmStatus;
use App\Models\Genre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FilmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++)
        {
            Film::factory()
                ->count(1)
                ->for(Director::factory()->state([
                    'id' => $i
                ]))
                ->for(FilmStatus::factory()->state([
                    'status' => 'ready'
                ]))
                ->hasAttached(Actor::factory()->count(3))
                ->hasAttached(Genre::factory()->count(2))
                ->create();
        }

    }
}
