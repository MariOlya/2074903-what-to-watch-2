<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Color extends Model
{
    use HasFactory;

    public function withBackgroundColorFilms(): BelongsTo
    {
        return $this->belongsTo(Film::class, 'background_color_id')->withDefault();
    }
}
