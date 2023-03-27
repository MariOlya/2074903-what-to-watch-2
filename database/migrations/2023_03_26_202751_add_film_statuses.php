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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("SET foreign_key_checks=0");
        DB::table('film_statuses')->truncate();
        DB::statement("SET foreign_key_checks=1");
    }
};
