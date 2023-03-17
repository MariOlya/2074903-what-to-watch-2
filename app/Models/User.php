<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Model
{
    protected $with = ['avatar', 'role'];

    use HasFactory;

    public function avatar() : HasOne
    {
        return $this->hasOne(File::class, 'avatar_id')->withDefault();
    }

    public function role() : HasOne
    {
        return $this->hasOne(UserRole::class);
    }

    public function reviews(): BelongsTo
    {
        return $this->belongsTo(Review::class)->withDefault();
    }

    public function comments(): BelongsTo
    {
        return $this->belongsTo(Comment::class)->withDefault();
    }

    public function favoriteFilms(): BelongsToMany
    {
        return $this->belongsToMany(Film::class, 'favorite_films');
    }
}
