<?php

namespace App\Providers;

use App\Models\Film;
use App\Models\Genre;
use App\Models\Review;
use App\Policies\FilmPolicy;
use App\Policies\GenrePolicy;
use App\Policies\ReviewPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Genre::class => GenrePolicy::class,
        Film::class => FilmPolicy::class,
        Review::class => ReviewPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
