<?php

namespace App\Jobs;

use App\Factories\Dto\FilmApiDto;
use App\Repositories\Interfaces\FilmApiRepositoryInterface;
use App\Repositories\Interfaces\FilmRepositoryInterface;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ParseFilmInfo implements ShouldQueue
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
        FilmApiRepositoryInterface $apiRepository,
        FilmRepositoryInterface $filmRepository,
    ): void {
        $response = $apiRepository->getMovieInfoById($this->imdbId);

        if ($response['code'] !== 200) {
            throw new \RuntimeException(
                message: $response['message'],
                code: $response['code']
            );
        }

        $filmInfo = (array)$response['data'];

        $filmRepository->fillFilmInfo($this->imdbId, new FilmApiDto($filmInfo));
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
