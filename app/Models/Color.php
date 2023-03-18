<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Color extends Model
{
    public $timestamps = false;

    use HasFactory;

    public function withBackgroundColorFilms(): HasMany
    {
        return $this->HasMany(Film::class, 'background_color_id');
    }
}
