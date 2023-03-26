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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('link_types')->truncate();
    }
};
