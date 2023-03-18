<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++)
        {
            Comment::factory()
                ->count(1)
                ->for(User::factory()->state([
                    'id' => random_int(1, 10),
                ]))
                ->for(Review::factory()->state([
                    'id' => random_int(1, 40),
                ]))
                ->create();
        }
    }
}
