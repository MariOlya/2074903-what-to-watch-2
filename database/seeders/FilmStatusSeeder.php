<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FilmStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filmStatuses = [
            'pending',
            'moderate',
            'ready'
        ];

        foreach ($filmStatuses as $filmStatus) {
            DB::table('film_statuses')->insert([
                'status' => $filmStatus
            ]);
        }
    }
}
