<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FileType extends Model
{
    public $timestamps = false;

    use HasFactory;

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

}
