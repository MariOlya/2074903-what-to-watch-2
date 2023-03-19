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
        $films = Film::all('id')->map(function (Film $film) {
            return $film->id;
        })->toArray();

        $users = User::all('id')->map(function (User $user) {
            return $user->id;
        })->toArray();

        foreach ($users as $user) {
            $randomFilms = array_rand($films, 4);

            foreach ($randomFilms as $randomFilm) {
                Review::factory()
                    ->count(1)
                    ->create([
                        'user_id' => User::whereId($user)->value('id'),
                        'film_id' => Film::whereId($films[$randomFilm])->value('id'),
                    ]);
            }
        }
    }
}
