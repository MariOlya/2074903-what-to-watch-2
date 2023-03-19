<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\LinkType
 *
 * @property int $id
 * @property string $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Link> $links
 * @property-read int|null $links_count
 * @method static \Illuminate\Database\Eloquent\Builder|LinkType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LinkType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LinkType query()
 * @method static \Illuminate\Database\Eloquent\Builder|LinkType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LinkType whereType($value)
 * @mixin \Eloquent
 */
class LinkType extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function links(): HasMany
    {
        return $this->hasMany(Link::class);
    }
}
