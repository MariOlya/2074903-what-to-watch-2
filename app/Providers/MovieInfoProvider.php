<?php

namespace App\Providers;

use App\Custom\MovieByIdInfoRepository;
use App\Custom\MovieInfoRepository;
use App\Custom\OmdbMovieInfoRepository;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider;

class MovieInfoProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(MovieInfoRepository::class, OmdbMovieInfoRepository::class);
        $this->app->bind(ClientInterface::class, Client::class);
    }

    public function boot(): void
    {

    }
}
