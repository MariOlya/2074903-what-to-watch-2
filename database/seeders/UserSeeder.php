<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->count(1)
            ->create([
                'user_role_id' => UserRole::whereRole('admin')->value('id'),
            ]);

        User::factory()
            ->count(2)
            ->create([
                'user_role_id' => UserRole::whereRole('moderator')->value('id'),
            ]);
        User::factory()
            ->count(7)
            ->create([
                'user_role_id' => UserRole::whereRole('usual')->value('id'),
            ]);

    }
}
