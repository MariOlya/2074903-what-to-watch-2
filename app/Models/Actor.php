<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Actor extends Model
{
    public $timestamps = false;

    use HasFactory;

    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class);
    }
}
