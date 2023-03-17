<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Review extends Model
{
    protected $with = ['user', 'film'];

    use HasFactory;

    public function user(): HasOne
    {
        return $this->hasOne(User::class)->withDefault();
    }

    public function film(): HasOne
    {
        return $this->hasOne(Film::class);
    }

    public function comments(): BelongsTo
    {
        return $this->belongsTo(Comment::class)->withDefault();
    }
}
