<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->count(1)
            ->for(UserRole::factory()->state([
                'role' => 'admin',
            ]))->create();

        User::factory()
            ->count(2)
            ->for(UserRole::factory()->state([
                'role' => 'moderator',
            ]))->create();

        User::factory()
            ->count(7)
            ->for(UserRole::factory()->state([
                'role' => 'usual',
            ]))->create();
    }
}
