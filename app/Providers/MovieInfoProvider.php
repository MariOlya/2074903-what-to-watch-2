<?php

namespace App\Providers;

use App\Custom\OmdbMovieByIdRepository;
use App\Custom\OmdbMovieRepository;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider;

class MovieInfoProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(OmdbMovieRepository::class, OmdbMovieByIdRepository::class);
        $this->app->bind(ClientInterface::class, Client::class);
    }

    public function boot(): void
    {

    }
}
