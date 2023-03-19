<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LinkTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $linkTypes = [
            'video',
            'preview'
        ];

        foreach ($linkTypes as $linkType) {
            DB::table('link_types')->insert([
                'type' => $linkType
            ]);
        }
    }
}
