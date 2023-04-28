<?php

namespace App\Rules;

use App\Models\Review;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsExistReview implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!Review::whereId($value)->first()) {
            $fail($attribute.' is not found.');
        }
    }
}
