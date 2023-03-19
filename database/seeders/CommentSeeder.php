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
        $reviews = Review::all('id')->map(function (Review $review) {
            return $review->id;
        })->toArray();

        $users = User::all('id')->map(function (User $user) {
            return $user->id;
        })->toArray();

        foreach ($users as $user) {
            $randomReviews = array_rand($reviews, 2);

            foreach ($randomReviews as $randomReview) {
                Comment::factory()
                    ->count(1)
                    ->create([
                        'user_id' => User::whereId($user)->value('id'),
                        'review_id' => Review::whereId($reviews[$randomReview])->value('id'),
                    ]);
            }
        }
    }
}
