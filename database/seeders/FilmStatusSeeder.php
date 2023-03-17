<?php

namespace Database\Seeders;

use App\Models\FilmStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FilmStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FilmStatus::factory()->count(3)->create();
    }
}
