<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\FilmRepository;
use App\Repositories\GenreRepository;
use App\Repositories\Interfaces\FilmRepositoryInterface;
use App\Repositories\Interfaces\GenreRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(FilmRepositoryInterface::class, FilmRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(GenreRepositoryInterface::class, GenreRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
