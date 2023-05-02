<?php

namespace App\Providers;

use App\Custom\MovieByIdInfoRepository;
use App\Repositories\CommentsApiRepository;
use App\Repositories\HtmlAcademyFilmApiRepository;
use App\Repositories\Interfaces\CommentsApiRepositoryInterface;
use App\Repositories\Interfaces\HtmlAcademyFilmApiRepositoryInterface;
use App\Repositories\Interfaces\OmdbFilmApiRepositoryInterface;
use App\Repositories\OmdbFilmApiRepository;
use App\Repositories\Interfaces\FilmApiRepositoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider;

class ExternalApiProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(OmdbFilmApiRepositoryInterface::class, OmdbFilmApiRepository::class);
        $this->app->bind(HtmlAcademyFilmApiRepositoryInterface::class, HtmlAcademyFilmApiRepository::class);
        $this->app->bind(ClientInterface::class, Client::class);
        $this->app->bind(CommentsApiRepositoryInterface::class, CommentsApiRepository::class);
    }

    public function boot(): void
    {

    }
}
