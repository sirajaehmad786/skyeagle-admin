<?php

namespace App\Http\Requests;

use App\Models\TourBookingRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TourBookingRequestUpdateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(TourBookingRequest::statuses())],
            'admin_note' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
