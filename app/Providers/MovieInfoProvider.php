<?php

namespace App\Providers;

use App\Custom\MovieByIdInfoRepository;
use App\Repositories\FilmApiRepository;
use App\Repositories\Interfaces\FilmApiRepositoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider;

class MovieInfoProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(FilmApiRepositoryInterface::class, FilmApiRepository::class);
        $this->app->bind(ClientInterface::class, Client::class);
    }

    public function boot(): void
    {

    }
}
