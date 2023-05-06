<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rennokki\QueryCache\Traits\QueryCacheable;

/**
 * App\Models\Director
 *
 * @property int $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Film> $films
 * @property-read int|null $films_count
 * @method static \Database\Factories\DirectorFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Director newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Director newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Director query()
 * @method static \Illuminate\Database\Eloquent\Builder|Director whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Director whereName($value)
 * @mixin \Eloquent
 */
class Director extends Model
{
    use HasFactory, QueryCacheable;

    public $cacheFor = 24*60*60;
    public $cacheTags = ['director'];

    public $timestamps = false;

    public $fillable = ['name'];

    protected function cacheForValue()
    {
        if (request()?->user()?->userRole->role === User::ADMIN_ROLE) {
            return null;
        }

        return $this->cacheFor;
    }

    public function films(): HasMany
    {
        return $this->hasMany(Film::class);
    }
}
