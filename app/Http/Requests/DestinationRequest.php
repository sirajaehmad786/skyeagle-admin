<?php

namespace App\Http\Requests;

use App\Models\Destination;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DestinationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $destinationId = $this->route('destination');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('destinations', 'slug')->ignore($destinationId),
            ],
            'country' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'banner_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'description' => ['nullable', 'string'],
            'best_time_to_visit' => ['nullable', 'string', 'max:500'],
            'popular_attractions' => ['nullable', 'array'],
            'popular_attractions.*' => ['nullable', 'string', 'max:255'],
            'faq_question' => ['nullable', 'array'],
            'faq_question.*' => ['nullable', 'string', 'max:255'],
            'faq_answer' => ['nullable', 'array'],
            'faq_answer.*' => ['nullable', 'string'],
            'status' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (blank($this->slug) && filled($this->name)) {
            $this->merge([
                'slug' => Destination::uniqueSlug(
                    $this->name,
                    $this->route('destination') ? (int) $this->route('destination') : null
                ),
            ]);
        }
    }
}
