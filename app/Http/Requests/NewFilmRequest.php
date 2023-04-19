<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Requests\Api\FormRequest;
use App\Models\Film;
use Illuminate\Validation\Rule;

class NewFilmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Film::class);
    }

    public function rules(): array
    {
        return [
            'imdb_id' => [
                'required',
                Rule::unique(Film::class),
                'string',
                'max:20',
                'regex:/ev\d{7}\/(19|20)\d{2}(\/[12])?|tt\d{7,8}\/characters\/nm\d{7,8}|(tt|ni|nm)\d{8}|(ch|co|ev|tt|nm)\d{7}/'
            ]
        ];
    }
}
