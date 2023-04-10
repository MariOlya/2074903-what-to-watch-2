<?php

declare(strict_types=1);

namespace App\Providers;

use App\Factories\FilmFactory;
use App\Factories\FilmImageFactory;
use App\Factories\Interfaces\FilmFactoryInterface;
use App\Factories\Interfaces\FilmFileFactoryInterface;
use App\Factories\Interfaces\LinkFactoryInterface;
use App\Factories\Interfaces\UserFactoryInterface;
use App\Factories\LinkFactory;
use App\Factories\UserFactory;
use Illuminate\Support\ServiceProvider;

class FactoryProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(UserFactoryInterface::class, UserFactory::class);
        $this->app->bind(FilmFactoryInterface::class, FilmFactory::class);
        $this->app->bind(FilmFileFactoryInterface::class, FilmImageFactory::class);
        $this->app->bind(LinkFactoryInterface::class, LinkFactory::class);
    }

}
