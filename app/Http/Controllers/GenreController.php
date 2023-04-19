<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GenreRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\UnauthorizedResponse;
use App\Models\Genre;
use App\Repositories\Interfaces\GenreRepositoryInterface;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function __construct(
        readonly GenreRepositoryInterface $genreRepository,
    )
    {
    }

    /**
     * POLICY: No policy, technical endpoint
     *
     * @return BaseResponse
     */
    public function getGenres(): BaseResponse
    {
        $genres = $this->genreRepository->all();

        return new SuccessResponse(
            data: [
                'genres' => $genres,
            ]
        );
    }

    /**
     * POLICY: Only auth user + only moderator (rules in GenrePolicy and GenreRequest)
     *
     * @param GenreRequest $request
     * @param Genre $genre
     * @return BaseResponse
     */
    public function updateGenre(GenreRequest $request, Genre $genre): BaseResponse
    {
        $validated = $request->validated();
        $genreId = $genre->id;

        $updatedGenre = $this->genreRepository->update($genreId, $validated['genre']);

        return new SuccessResponse(
            data: [
                'updatedGenre' => $updatedGenre,
            ]
        );
    }
}
