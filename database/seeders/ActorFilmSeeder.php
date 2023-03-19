<?php

namespace Database\Seeders;

use App\Models\Actor;
use App\Models\Film;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActorFilmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $films = Film::all('id')->map(function (Film $film) {
            return $film->id;
        })->toArray();

        $actors = Actor::all('id')->map(function (Actor $actor) {
            return $actor->id;
        })->toArray();

        foreach ($films as $film) {
            $randomActors = array_rand($actors, 3);

            foreach ($randomActors as $randomActor) {
                DB::table('actor_film')->insert([
                    'actor_id' => Actor::whereId($actors[$randomActor])->value('id'),
                    'film_id' => Film::whereId($film)->value('id'),
                ]);
            }
        }
    }
}
