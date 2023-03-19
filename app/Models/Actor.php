<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Actor
 *
 * @property int $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Film> $films
 * @property-read int|null $films_count
 * @method static \Database\Factories\ActorFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Actor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Actor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Actor query()
 * @method static \Illuminate\Database\Eloquent\Builder|Actor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Actor whereName($value)
 * @mixin \Eloquent
 */
class Actor extends Model
{
    public $timestamps = false;

    use HasFactory;

    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class);
    }
}
