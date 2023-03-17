<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilmStatus extends Model
{
    use HasFactory;

    public function films(): BelongsTo
    {
        return $this->belongsTo(Film::class, 'status_id');
    }
}
