<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class File extends Model
{
    public $timestamps = false;

    use HasFactory;

    public function type(): HasOne
    {
        return $this->hasOne(FileType::class);
    }

    /** Just for file type 'avatar'
     * Potentially we should extend this method with 'where'
     */
    public function withAvatarUsers(): BelongsTo
    {
        return $this->belongsTo(User::class, 'avatar_id')->withDefault();
    }

    /** Just for file type 'poster'
     * Potentially we should extend this method with 'where'
     */
    public function withPosterFilms(): BelongsTo
    {
        return $this->belongsTo(Film::class, 'poster_image_id')->withDefault();
    }

    /** Just for file type 'preview'
     * Potentially we should extend this method with 'where'
     */
    public function withPreviewFilms(): BelongsTo
    {
        return $this->belongsTo(Film::class, 'preview_image_id')->withDefault();
    }

    /** Just for file type 'background'
     * Potentially we should extend this method with 'where'
     */
    public function withBackgroundFilms(): BelongsTo
    {
        return $this->belongsTo(Film::class, 'background_image_id')->withDefault();
    }
}
