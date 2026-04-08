<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisaSaveRequest extends FormRequest
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
            "visa_country" => "required",
            "visa_category" => "required",
            "visa_type" => "required",
            "visa_travel_date" => "required|date",
            "visa_adults" => "required|numeric",
            "visa_child" => "required|numeric",
            "visa_infant" => "required|numeric",
            "price" => "required|numeric",
            "visa_adult_service_charge" => "required|numeric|min:0",
            "visa_child_service_charge" => "required|numeric|min:0"
        ];
    }
}
