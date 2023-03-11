<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/** Authentication actions */
Route::post('/login', [\App\Http\Controllers\AuthenticationController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\AuthenticationController::class, 'logout']);

/** Actions with user */
Route::post('/register', [\App\Http\Controllers\UserController::class, 'register']);
Route::get('/user', [\App\Http\Controllers\UserController::class, 'getUser']);
Route::patch('/user', [\App\Http\Controllers\UserController::class, 'updateUser']);

/** Lists of films */
Route::get('/films', [\App\Http\Controllers\FilmsController::class, 'getFilms']);
Route::get('/favorite', [\App\Http\Controllers\FilmsController::class, 'getFavoriteFilms']);
Route::get('/films/{filmId}/similar', [\App\Http\Controllers\FilmsController::class, 'getSimilarFilms']);

/** Actions with film */
Route::get('/films/{filmId}', [\App\Http\Controllers\FilmController::class, 'getFilmInfo']);
Route::post('/films/{filmId}/favorite', [\App\Http\Controllers\FilmController::class, 'addFavoriteFilm']);
Route::delete('/films/{filmId}/favorite', [\App\Http\Controllers\FilmController::class, 'deleteFavoriteFilm']);
Route::post('/films', [\App\Http\Controllers\FilmController::class, 'addNewFilm']);
Route::patch('/films/{filmId}', [\App\Http\Controllers\FilmController::class, 'updateFilm']);
Route::get('/films/{filmId}/comments', [\App\Http\Controllers\FilmController::class, 'getFilmComments']);

/** Actions with promo film */
Route::get('/promo', [\App\Http\Controllers\PromoController::class, 'getPromoFilm']);
Route::post('/promo/{filmId}', [\App\Http\Controllers\PromoController::class, 'setPromoFilm']);

/** Actions with genres */
Route::get('/genres', [\App\Http\Controllers\GenreController::class, 'getGenres']);
Route::patch('/genres/{genreId}', [\App\Http\Controllers\GenreController::class, 'updateGenre']);

/** Actions with review */
Route::post('/films/{filmId}/comments', [\App\Http\Controllers\ReviewController::class, 'addNewReview']);
Route::patch('/comments/{commentId}', [\App\Http\Controllers\ReviewController::class, 'updateReview']);
Route::delete('/comments/{commentId}', [\App\Http\Controllers\ReviewController::class, 'deleteReview']);
