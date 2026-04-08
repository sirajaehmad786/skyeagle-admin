<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuotationHotelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hotel_id.*'   => 'required',
            'check_in.*'   => 'required|date',
            'check_out.*'  => 'required|date|after_or_equal:check_in.*',
            // 'total_room.*'      => 'required|integer|min:1',
            // 'total_cnb.*'       => 'nullable|integer|min:0',
            // 'single_room.*'    => 'nullable|integer|min:0',
            // 'total_cwb.*'       => 'nullable|integer|min:0',
            // 'total_room_price.*' => 'required|numeric|min:0',
            // 'total_cnb_price.*'  => 'nullable|numeric|min:0',
            // 'single_room_price.*' => 'nullable|numeric|min:0',
            // 'total_cwb_price.*'   => 'nullable|numeric|min:0',

            // Quotation-level service charges (per person)
            'double_room_service_price' => 'nullable|numeric|min:0',
            'triple_room_service_price' => 'nullable|numeric|min:0',
            'total_cnb_service_price'   => 'nullable|numeric|min:0',
            'single_room_service_price' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'hotel_id.*.required'   => '',
            'check_in.*.required'   => '',
            'check_out.*.required'  => '',
            'total_room.*.required' => '',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    
}
