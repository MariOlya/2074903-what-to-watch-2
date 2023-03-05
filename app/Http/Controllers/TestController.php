<?php

namespace App\Http\Controllers;

use App\Custom\MovieResult;

class TestController extends Controller
{
    public function movie(MovieResult $movieResult)
    {
        $movies = [
            'tt2262345',
            'tt1196946',
            'tt0207275',
            'tt1270080',
            'tt2582496'
        ];

        foreach ($movies as $movie) {
            $movieInfo = $movieResult->getMovieInfo($movie);
            echo '<pre>';
            var_dump($movie, $movieInfo);
        }
        die();
    }

}
