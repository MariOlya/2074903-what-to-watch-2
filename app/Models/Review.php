<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Review
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $user_id
 * @property int $film_id
 * @property string $text
 * @property int $rating
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $comments
 * @property-read \App\Models\Review $review
 * @property-read int|null $comments_count
 * @property-read \App\Models\Film $film
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\ReviewFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Review onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Review query()
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereFilmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Review withoutTrashed()
 * @mixin \Eloquent
 */
class Review extends Model
{
    use HasFactory, SoftDeletes;

    public const REVIEW_DEFAULT_ORDER_TO = 'desc';
    public const REVIEW_DEFAULT_ORDER_BY = 'created_at';

    public $fillable = [
        'text',
        'rating',
        'review_id',
        'user_id',
        'film_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Гость'
        ]);
    }

    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(__CLASS__);
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(__CLASS__);
    }
}
