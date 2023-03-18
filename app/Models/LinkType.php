<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LinkType extends Model
{
    public $timestamps = false;

    use HasFactory;

    public function links(): HasMany
    {
        return $this->hasMany(Link::class);
    }
}
