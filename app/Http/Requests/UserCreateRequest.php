<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserCreateRequest extends FormRequest
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
            "password" => ['required',
                            Password::min(8) // Minimum length
                            ->letters()  // Must contain at least one letter
                            ->mixedCase() // Must contain uppercase AND lowercase letters
                            ->numbers()   // Must contain at least one number
                        ],
            "role" => "required",
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
