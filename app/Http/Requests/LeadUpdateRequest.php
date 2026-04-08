<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class LeadUpdateRequest extends FormRequest
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
            'query_type' => 'required',
            'no_of_kids' => 'numeric',
            'no_of_adults' => 'required|numeric',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $travel = $this->input('travel_type');
            $hasFlight = $this->input('flight_requirements') === 'Yes';
            $hasHotel = $this->input('hotel_requirements') === 'Yes';
            $hasSightseeing = $this->input('sightseeing_requirements') === 'Yes';
            $hasVisa = $travel === 'International' && $this->input('visa_requirements') === 'Yes';

            if (! $hasFlight && ! $hasHotel && ! $hasSightseeing && ! $hasVisa) {
                // Use flight_requirements so AJAX error handler attaches feedback to a visible field
                $validator->errors()->add(
                    'flight_requirements',
                    'Please set at least one package service to Yes: Flight, Hotel, Sightseeing, or Visa (for international trips).'
                );
            }
        });
    }
}
