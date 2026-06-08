<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "first_name" => "required|max:50",
            "last_name" => "required|max:50",
            "email" => "required|max:100|email|unique:users,email",
            'email' => [
                        'required',
                        'email',
                        Rule::unique('users', 'email')->ignore($this->route('user')),
                    ],
            "password" => ['nullable',
                            Password::min(8)
                            ->letters()
                            ->mixedCase()
                            ->numbers()
                        ],
            "phone" => "required|digits_between:10,15",
            // "al_phone" => "digits_between:10,15",
            "status" => "required"
        ];
    }

    // public function messages()
    // {
    //     // return [
    //     //     "phone.required" =
    //     // ]
    // }
}
