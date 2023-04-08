<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Requests\Api\FormRequest;
use App\Models\Film;
use App\Models\FilmStatus;
use Illuminate\Validation\Rule;

class UpdatingFilmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->film);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'poster_image' => [
                'string',
                'max:255',
                'regex:/^(img\/)?[a-z0-9]+([\-\.][a-z0-9]+)*\.[a-z]{2,5}$/'
            ],
            'preview_image' => [
                'string',
                'max:255',
                'regex:/^(img\/)?[a-z0-9]+([\-\.][a-z0-9]+)*\.[a-z]{2,5}$/'
            ],
            'background_image' => [
                'string',
                'max:255',
                'regex:/^(img\/)?[a-z0-9]+([\-\.][a-z0-9]+)*\.[a-z]{2,5}$/'
            ],
            'background_color' => [
                'string',
                'max:9',
                'regex:/^#[A-Za-z0-9]{6,6}$/'
            ],
            'video_link' => [
                'string',
                'max:255',
                'url'
            ],
            'preview_video_link' => [
                'string',
                'max:255',
                'url'
            ],
            'description' => [
                'string',
                'max:1000'
            ],
            'director' => [
                'string',
                'max:255',
                'regex:/^[A-Za-zА-Яа-яЁё\s]{2,50}$/u'
            ],
            'starring' => [
                'array'
            ],
            'genre' => [
                'array'
            ],
            'run_time' => [
                'integer'
            ],
            'released' => [
                'integer',
                'between:1895,2033'
            ],
            'imdb_id' => [
                'required',
                Rule::unique(Film::class),
                'string',
                'max:20',
                'regex:/ev\d{7}\/(19|20)\d{2}(\/[12])?|tt\d{7,8}\/characters\/nm\d{7,8}|(tt|ni|nm)\d{8}|(ch|co|ev|tt|nm)\d{7}/'
            ],
            'status' => [
                'required',
                'string',
                Rule::in([array_map(
                    static fn ($status) => $status['status'],
                    FilmStatus::all('status')->toArray()
                )])
            ]
        ];
    }


}
