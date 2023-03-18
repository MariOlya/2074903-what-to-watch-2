<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Film extends Model
{
    protected $with = [
        'posterImage',
        'previewImage',
        'backgroundImage',
        'backgroundColor',
        'videoLink',
        'previewVideoLink',
        'director',
        'status',
        'actors',
        'genres'
    ];

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
        return $this->hasMany(Review::class)->withDefault();
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
