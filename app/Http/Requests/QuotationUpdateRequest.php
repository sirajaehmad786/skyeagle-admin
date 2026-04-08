<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuotationUpdateRequest extends FormRequest
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
            "start_date" => "required|date",
            "end_date" => "required|date",
            "company_id" => "required",
            "amount_description_services" => "nullable|array",
            "amount_description_services.*" => "nullable|in:flight,visa,hotel,sightseeing",
            'discount' => 'nullable|numeric|min:0|max:99999'
        ];
    }
}
