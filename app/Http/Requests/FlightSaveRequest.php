<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlightSaveRequest extends FormRequest
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
            //"travel_mode" => "required",
            //"trip_type" => "required",
            // "flight_end_date" => "required|date",
            //"flight_start_date" => "required|date",
            //"flight_source_city" => "required",
            //"flight_destination_city" => "required",
            //"flight_adults" => "required|numeric",
            //"flight_child" => "required|numeric",
            // 'flight_multi_from.*' => 'required|not_in:0',
            // 'flight_multi_to.*'   => 'required|not_in:0|different:flight_multi_from.*',
            // 'flight_multi_date.*' => 'required|date|after_or_equal:today',
        ];
    }

    // public function messages(){
    //     // return [
    //     //     'flight_multi_from.*.required' => 'From city is required.',
    //     //     'flight_multi_from.*.not_in'   => 'Please select a valid from city.',

    //     //     'flight_multi_to.*.required'   => 'To city is required.',
    //     //     'flight_multi_to.*.not_in'     => 'Please select a valid to city.',
    //     //     'flight_multi_to.*.different'  => 'From and To cities must be different.',

    //     //     'flight_multi_date.*.required'        => 'Travel date is required.',
    //     //     'flight_multi_date.*.date'            => 'Please enter a valid date.',
    //     //     'flight_multi_date.*.after_or_equal'  => 'The travel date must be today or later.',
    //     // ];
    // }
}
