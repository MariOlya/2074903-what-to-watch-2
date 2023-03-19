<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Film
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $imdb_id
 * @property int|null $poster_image_id
 * @property int|null $preview_image_id
 * @property int|null $background_image_id
 * @property int|null $background_color_id
 * @property string|null $name
 * @property int|null $released
 * @property string|null $description
 * @property int|null $director_id
 * @property int|null $run_time
 * @property int|null $video_link_id
 * @property int|null $preview_video_link_id
 * @property float|null $rating
 * @property int|null $vote_amount
 * @property int $status_id
 * @property int $promo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Actor> $actors
 * @property-read int|null $actors_count
 * @property-read \App\Models\Color|null $backgroundColor
 * @property-read \App\Models\File|null $backgroundImage
 * @property-read \App\Models\Director|null $director
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Genre> $genres
 * @property-read int|null $genres_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $lovers
 * @property-read int|null $lovers_count
 * @property-read \App\Models\File|null $posterImage
 * @property-read \App\Models\File|null $previewImage
 * @property-read \App\Models\Link|null $previewVideoLink
 * @property-read \App\Models\FilmStatus $status
 * @property-read \App\Models\Link|null $videoLink
 * @method static \Database\Factories\FilmFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Film newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Film newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Film onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Film query()
 * @method static \Illuminate\Database\Eloquent\Builder|Film whereBackgroundColorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film whereBackgroundImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film whereDirectorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film whereImdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film wherePosterImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film wherePreviewImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film wherePreviewVideoLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film wherePromo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film whereReleased($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film whereRunTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film whereVideoLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film whereVoteAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Film withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Film withoutTrashed()
 * @mixin \Eloquent
 */
class Film extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function posterImage(): BelongsTo
    {
        return $this->belongsTo(File::class, 'poster_image_id')->withDefault();
    }

    public function previewImage(): BelongsTo
    {
        return $this->belongsTo(File::class, 'preview_image_id')->withDefault();
    }

    public function backgroundImage(): BelongsTo
    {
        return $this->belongsTo(File::class, 'background_image_id')->withDefault();
    }

    public function backgroundColor(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'background_color_id')->withDefault();
    }

    public function videoLink(): BelongsTo
    {
        return $this->belongsTo(Link::class, 'video_link_id')->withDefault();
    }

    public function previewVideoLink(): BelongsTo
    {
        return $this->belongsTo(Link::class, 'preview_video_link_id')->withDefault();
    }

    public function director(): BelongsTo
    {
        return $this->belongsTo(Director::class)->withDefault();
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(FilmStatus::class, 'status_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function lovers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite_films');
    }
}
