<?php

namespace App\Jobs;

use App\Factories\Dto\HtmlAcademyFilmApiDto;
use App\Factories\Dto\OmdbFilmApiDto;
use App\Repositories\Interfaces\FilmApiRepositoryInterface;
use App\Repositories\Interfaces\FilmRepositoryInterface;
use App\Repositories\Interfaces\HtmlAcademyFilmApiRepositoryInterface;
use App\Repositories\Interfaces\OmdbFilmApiRepositoryInterface;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ParseFilmInfoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 10;
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        readonly string $imdbId,
    ) {
    }

    /**
     * Execute the job.
     * @throws Exception
     */
    public function handle(
        OmdbFilmApiRepositoryInterface $omdbApiRepository,
        HtmlAcademyFilmApiRepositoryInterface $htmlAcademyApiRepository,
        FilmRepositoryInterface $filmRepository,
    ): void {
        $omdbResponse = $omdbApiRepository->getMovieInfoById($this->imdbId);

        if ($omdbResponse['code'] !== 200) {
            throw new \RuntimeException(
                message: $omdbResponse['message'],
                code: $omdbResponse['code']
            );
        }

        $filmInfo = (array)$omdbResponse['data'];
        $emptyRowData = 'N/A';

        $title = $filmInfo['Title'] ?? null;
        $released = $filmInfo['Released'] ?? null;
        $runTime = $filmInfo['Runtime'] ?? null;
        $genres = $filmInfo['Genre'] ?? null;
        $director = $filmInfo['Director'] ?? null;
        $actors = $filmInfo['Actors'] ?? null;
        $description = $filmInfo['Plot'] ?? null;
        $posterImage = $filmInfo['Poster'] ?? null;
        $rating = $filmInfo['imdbRating'] ?? null;
        $votes = $filmInfo['imdbVotes'] ?? null;

        $omdbFilmApiDto = new OmdbFilmApiDto(
            title: $title === $emptyRowData ? null : $title,
            released: $released === $emptyRowData ? null : $released,
            runTime: $runTime === $emptyRowData ? null : $runTime,
            genres: $genres === $emptyRowData ? null : $genres,
            director: $director === $emptyRowData ? null : $director,
            actors: $actors === $emptyRowData ? null : $actors,
            description: $description === $emptyRowData ? null : $description,
            posterImage: $posterImage === $emptyRowData ? null : $posterImage,
            rating: $rating === $emptyRowData ? null : $rating,
            amountVotes: $votes === $emptyRowData ? null : $votes
        );

        $filmRepository->fillFilmInfo($this->imdbId, $omdbFilmApiDto);

        $htmlAcademyResponse = $htmlAcademyApiRepository->getMovieInfoById($this->imdbId);

        if ($htmlAcademyResponse['code'] === 200) {
            $additionalFilmInfo = (array)$htmlAcademyResponse['data'];

            $htmlAcademyFilmApiDto = new HtmlAcademyFilmApiDto(
                title: $additionalFilmInfo['name'] ?? null,
                previewImage: $additionalFilmInfo['icon'] ?? null,
                backgroundImage: $additionalFilmInfo['background'] ?? null,
                videoLink: $additionalFilmInfo['video'] ?? null,
                previewVideoLink: $additionalFilmInfo['preview'] ?? null
            );

            $filmRepository->fillAdditionalFilmInfo($this->imdbId, $htmlAcademyFilmApiDto);
        }
    }

    /**
     * @param Exception $exception
     * @return void
     */
    public function failed(Exception $exception): void
    {
        Log::warning(
            'Your job to fill film ('.$this->imdbId.') info did not work. There is exception: '.$exception->getMessage()
        );
    }
}
