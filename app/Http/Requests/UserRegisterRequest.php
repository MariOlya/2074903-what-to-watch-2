<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Password;

class UserRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-zА-Яа-яЁё\s]{2,50}$/u'
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:50',
                $this->getUniqueRule(),
            ],
            'password' => [
                $this->getPasswordRequiredRule(),
                'string',
                'confirmed',
                Password::min(8),
            ],
            'file' => [
                'nullable',
                File::image()->max(10240)
            ]
        ];
    }

    private function getUniqueRule(): \Illuminate\Validation\Rules\Unique
    {
        $rule = Rule::unique(User::class);

        if ($this->isMethod('patch') && Auth::check()) {
            return $rule->ignore(Auth::user());
        }

        return $rule;
    }

    private function getPasswordRequiredRule(): string
    {
        return $this->isMethod('patch') ? 'sometimes' : 'required';
    }
}
