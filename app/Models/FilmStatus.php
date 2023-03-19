<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\FilmStatus
 *
 * @property int $id
 * @property string $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Film> $films
 * @property-read int|null $films_count
 * @method static \Database\Factories\FilmStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|FilmStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FilmStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FilmStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|FilmStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FilmStatus whereStatus($value)
 * @mixin \Eloquent
 */
class FilmStatus extends Model
{
    public $timestamps = false;

    use HasFactory;

    public function films(): HasMany
    {
        return $this->hasMany(Film::class, 'status_id');
    }
}
