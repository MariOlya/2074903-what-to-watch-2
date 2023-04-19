<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Requests\Api\FormRequest;
use App\Models\Review;
use Illuminate\Validation\Rule;

class ReviewRequest extends FormRequest
{
    public function findReview(): ?Review
    {
        return Review::query()->find($this->route('comment'));
    }

    public function authorize(): bool
    {
        if ($this->isMethod('patch')) {
            $review = $this->findReview();
            return $this->user()->can('update', $review);
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'text' => [
                'required',
                'string',
                'between:50,400'
            ],
            'rating' => [
                $this->getRatingRequiredRule(),
                'nullable',
                'integer',
                'between:1,10'
            ],
            'comment_id' => [
                'integer',
                Rule::in(array_map(
                    static fn ($review) => $review['id'],
                    Review::all('id')->toArray()
                ))
            ],
        ];
    }

    /**
     * @return string
     */
    private function getRatingRequiredRule(): string
    {
        return $this->isMethod('patch') ?
            'sometimes' :
            'required_without:comment_id';
    }
}
