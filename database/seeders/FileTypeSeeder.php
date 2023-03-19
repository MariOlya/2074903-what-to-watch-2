<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FileTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fileTypes = [
            'avatar',
            'poster',
            'preview',
            'background'
        ];

        foreach ($fileTypes as $fileType) {
            DB::table('file_types')->insert([
                'type' => $fileType
            ]);
        }
    }
}
