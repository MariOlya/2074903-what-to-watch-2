<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Genre
 *
 * @property int $id
 * @property string $genre
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Film> $films
 * @property-read int|null $films_count
 * @method static \Database\Factories\GenreFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Genre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Genre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Genre query()
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereGenre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereId($value)
 * @mixin \Eloquent
 */
class Genre extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = ['genre'];

    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class);
    }
}
