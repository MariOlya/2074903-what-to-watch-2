<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Requests\Api\FormRequest;

class GenreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'genre' => [
                'required',
                'string',
                'max:50',
                'lowercase'
            ],
        ];
    }
}
