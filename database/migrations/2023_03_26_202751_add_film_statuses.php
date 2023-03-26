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
        DB::table('film_statuses')->truncate();
    }
};
