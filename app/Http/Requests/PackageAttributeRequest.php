<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PackageAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $attributeId = $this->route('package_attribute');

        return [
            'type' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/'],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'slug' => [
                Rule::unique('package_attributes', 'slug')
                    ->where(fn ($query) => $query->where('type', $this->type))
                    ->ignore($attributeId),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('type')) {
            $this->merge(['type' => Str::slug($this->type, '_')]);
        }

        if ($this->filled('name')) {
            $this->merge(['slug' => Str::slug($this->name)]);
        }
    }
}
