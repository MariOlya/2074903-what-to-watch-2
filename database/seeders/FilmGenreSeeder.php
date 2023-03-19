<?php

namespace Database\Seeders;

use App\Models\Actor;
use App\Models\Film;
use App\Models\Genre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FilmGenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $films = Film::all('id')->map(function (Film $film) {
            return $film->id;
        })->toArray();

        $genres = Genre::all('id')->map(function (Genre $genre) {
            return $genre->id;
        })->toArray();

        foreach ($films as $film) {
            $randomGenres = array_rand($genres, 2);

            foreach ($randomGenres as $randomGenre) {
                DB::table('film_genre')->insert([
                    'genre_id' => Genre::whereId($genres[$randomGenre])->value('id'),
                    'film_id' => Film::whereId($film)->value('id'),
                ]);
            }
        }
    }
}
