<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Requests\Api\FormRequest;
use App\Models\Genre;

class GenreRequest extends FormRequest
{
    public function authorize(): bool
    {
        $genre = $this->route('genre');

        return $genre && $this->user()->can('update', $genre);
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
