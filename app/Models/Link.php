<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Link extends Model
{
    public $timestamps = false;

    use HasFactory;

    public function type(): BelongsTo
    {
        return $this->belongsTo(LinkType::class);
    }

    public function withVideoLinkFilms(): HasMany
    {
        return $this->hasMany(Film::class, 'video_link_id');
    }

    public function withPreviewVideoLinkFilms(): HasMany
    {
        return $this->hasMany(Film::class, 'preview_video_link_id');
    }
}
