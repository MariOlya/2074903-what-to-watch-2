<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rennokki\QueryCache\Traits\QueryCacheable;

/**
 * App\Models\Link
 *
 * @property int $id
 * @property string $link
 * @property int $link_type_id
 * @property-read \App\Models\LinkType|null $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Film> $withPreviewVideoLinkFilms
 * @property-read int|null $with_preview_video_link_films_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Film> $withVideoLinkFilms
 * @property-read int|null $with_video_link_films_count
 * @method static \Database\Factories\LinkFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Link newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Link newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Link query()
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereLinkTypeId($value)
 * @mixin \Eloquent
 */
class Link extends Model
{
    use HasFactory, QueryCacheable;

    public $cacheFor = 24*60*60;
    public $cacheTags = ['link'];

    public $timestamps = false;

    public $fillable = ['link', 'link_type_id'];

    protected function cacheForValue()
    {
        if (request()?->user()?->userRole->role === User::ADMIN_ROLE) {
            return null;
        }

        return $this->cacheFor;
    }

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
