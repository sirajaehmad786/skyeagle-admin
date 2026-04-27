<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_date'          => 'required',
            'end_date'            => 'required|after_or_equal:start_date',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->start_date) {
            $this->merge([
                'start_date' => convertDateFormat($this->start_date),
            ]);
        }

        if ($this->end_date) {
            $this->merge([
                'end_date' => convertDateFormat($this->end_date),
            ]);
        }
    }
}
