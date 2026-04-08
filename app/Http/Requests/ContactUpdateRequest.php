<?php

namespace App\Http\Requests;

use App\Constants\ContactStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactUpdateRequest extends FormRequest
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
        $contactId = $this->route('contact');

        return [
            "first_name" => "required|max:50",
            "last_name" => "required|max:50",
            "mobile_no" => [
                "required",
                "digits:10",
                Rule::unique('contacts', 'mobile_no')->ignore($contactId),
            ],
            "lead_source" => "required",
            "status" => "nullable|in:" . implode(',', ContactStatus::all()),
            "address" => "max:255",
            "city" => "max:50",
            "state" => "max:50",
            "country" => "max:50",
            "pincode" => "max:50",
            "company_name" => "max:100",
            "gst_no" => "max:50",
            "pan_no" => "max:50",
        ];
    }
}
