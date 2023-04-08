<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Film;
use App\Models\Genre;
use App\Policies\FilmPolicy;
use App\Policies\GenrePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Genre::class => GenrePolicy::class,
        Film::class => FilmPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
