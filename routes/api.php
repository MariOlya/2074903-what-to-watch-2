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
Route::middleware('auth:sanctum')->post('/logout', [\App\Http\Controllers\AuthenticationController::class, 'logout']);

/** Actions with user */
Route::post('/register', [\App\Http\Controllers\UserController::class, 'register']);
Route::middleware('auth:sanctum')->get('/user', [\App\Http\Controllers\UserController::class, 'getUser']);
Route::middleware('auth:sanctum')->patch('/user', [\App\Http\Controllers\UserController::class, 'updateUser']);

/** Lists of films */
Route::get('/films', [\App\Http\Controllers\FilmsController::class, 'getFilms']);
Route::middleware('auth:sanctum')->get('/favorite', [\App\Http\Controllers\FilmsController::class, 'getFavoriteFilms']);
Route::get('/films/{id}/similar', [\App\Http\Controllers\FilmsController::class, 'getSimilarFilms']);

/** Actions with film */
Route::get('/films/{id}', [\App\Http\Controllers\FilmController::class, 'getFilmInfo']);
Route::middleware('auth:sanctum')->post('/films/{id}/favorite', [\App\Http\Controllers\FilmController::class, 'addFavoriteFilm']);
Route::middleware('auth:sanctum')->delete('/films/{id}/favorite', [\App\Http\Controllers\FilmController::class, 'deleteFavoriteFilm']);
Route::middleware('auth:sanctum')->post('/films', [\App\Http\Controllers\FilmController::class, 'addNewFilm']);
Route::middleware('auth:sanctum')->patch('/films/{id}', [\App\Http\Controllers\FilmController::class, 'updateFilm']);
Route::get('/films/{id}/comments', [\App\Http\Controllers\FilmController::class, 'getFilmReviews']);

/** Actions with promo film */
Route::get('/promo', [\App\Http\Controllers\PromoController::class, 'getPromoFilm']);
Route::middleware('auth:sanctum')->post('/promo/{id}', [\App\Http\Controllers\PromoController::class, 'setPromoFilm']);

/** Actions with genres */
Route::get('/genres', [\App\Http\Controllers\GenreController::class, 'getGenres']);
Route::middleware('auth:sanctum')->patch('/genres/{genre}', [\App\Http\Controllers\GenreController::class, 'updateGenre']);

/** Actions with review */
Route::middleware('auth:sanctum')->post('/films/{id}/comments', [\App\Http\Controllers\ReviewController::class, 'addNewReview']);
Route::middleware('auth:sanctum')->patch('/comments/{comment}', [\App\Http\Controllers\ReviewController::class, 'updateReview']);
Route::middleware('auth:sanctum')->delete('/comments/{comment}', [\App\Http\Controllers\ReviewController::class, 'deleteReview']);
