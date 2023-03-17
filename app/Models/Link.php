<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Link extends Model
{
    use HasFactory;

    public function type(): HasOne
    {
        return $this->hasOne(LinkType::class);
    }

    public function withVideoLinkFilms(): BelongsTo
    {
        return $this->belongsTo(Film::class, 'video_link_id')->withDefault();
    }

    public function withPreviewVideoLinkFilms(): BelongsTo
    {
        return $this->belongsTo(Film::class, 'preview_video_link_id')->withDefault();
    }
}
