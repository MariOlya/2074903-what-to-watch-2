<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Director extends Model
{
    public $timestamps = false;

    use HasFactory;

    public function films(): BelongsTo
    {
        return $this->belongsTo(Film::class)->withDefault();
    }
}
