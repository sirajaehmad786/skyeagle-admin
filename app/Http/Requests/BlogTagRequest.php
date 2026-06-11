<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tagId = $this->route('blog_tag');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('blog_tags', 'name')->ignore($tagId),
            ],
            'status' => ['required', Rule::in(array_keys($this->statusOptions()))],
        ];
    }

    protected function statusOptions(): array
    {
        return collect(config('constant.status', []))
            ->mapWithKeys(fn ($status) => [$status === config('constant.status.0', 'Active') ? 1 : 0 => $status])
            ->toArray();
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tag name is required',
            'name.unique' => 'Tag name already exists',
            'status.required' => 'Status is required',
        ];
    }
}
