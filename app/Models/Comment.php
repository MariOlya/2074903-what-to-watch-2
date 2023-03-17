<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Comment extends Model
{
    protected $with = ['user', 'review'];

    use HasFactory;

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
