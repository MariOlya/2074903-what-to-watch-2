<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("SET foreign_key_checks=0");
        DB::table('file_types')->truncate();
        DB::statement("SET foreign_key_checks=1");
    }
};
