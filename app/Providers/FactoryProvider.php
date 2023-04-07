<?php

declare(strict_types=1);

namespace App\Providers;

use App\Factories\FilmFactory;
use App\Factories\Interfaces\FilmFactoryInterface;
use App\Factories\Interfaces\UserFactoryInterface;
use App\Factories\UserFactory;
use Illuminate\Support\ServiceProvider;

class FactoryProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(UserFactoryInterface::class, UserFactory::class);
        $this->app->bind(FilmFactoryInterface::class, FilmFactory::class);
    }

}
