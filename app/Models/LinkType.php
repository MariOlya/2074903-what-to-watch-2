<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkType extends Model
{
    use HasFactory;

    public function links(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }
}
