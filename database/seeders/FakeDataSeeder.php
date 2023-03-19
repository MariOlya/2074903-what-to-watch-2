<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FakeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            ActorSeeder::class,
            GenreSeeder::class,
            UserSeeder::class,
            FilmSeeder::class,
            ActorFilmSeeder::class,
            FilmGenreSeeder::class,
            ReviewSeeder::class,
            CommentSeeder::class,
        ]);
    }
}
