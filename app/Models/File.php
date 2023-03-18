<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class File extends Model
{
    public $timestamps = false;

    use HasFactory;

    public function type(): BelongsTo
    {
        return $this->belongsTo(FileType::class);
    }

    /** Just for file type 'avatar'
     * Potentially we should extend this method with 'where'
     */
    public function withAvatarUsers(): HasMany
    {
        return $this->hasMany(User::class, 'avatar_id');
    }

    /** Just for file type 'poster'
     * Potentially we should extend this method with 'where'
     */
    public function withPosterFilms(): HasMany
    {
        return $this->hasMany(Film::class, 'poster_image_id')->withDefault();
    }

    /** Just for file type 'preview'
     * Potentially we should extend this method with 'where'
     */
    public function withPreviewFilms(): HasMany
    {
        return $this->hasMany(Film::class, 'preview_image_id')->withDefault();
    }

    /** Just for file type 'background'
     * Potentially we should extend this method with 'where'
     */
    public function withBackgroundFilms(): HasMany
    {
        return $this->hasMany(Film::class, 'background_image_id')->withDefault();
    }
}
