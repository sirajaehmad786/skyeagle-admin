<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $postId = $this->route('blog_post') ?? $this->route('blog_post_id') ?? $this->route('blog_post');
        $activeStatus = config('constant.status.0', 'Active');

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('blog_posts', 'title')->ignore($postId),
            ],
            'category_id' => ['nullable', 'exists:categories,id'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'author_name' => ['nullable', 'string', 'max:150'],
            'author_about' => ['nullable', 'string', 'max:1500'],
            'author_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_author_image' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(config('constant.status', []))],
            'is_featured' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'required_if:status,' . $activeStatus, 'date'],
            'reading_time_minutes' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'tags' => ['nullable', 'array', 'max:30'],
            'tags.*' => ['string', 'max:100'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'removed_images' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->published_at) {
            $this->merge([
                'published_at' => convertDateFormat($this->published_at),
            ]);
        }

        if (is_array($this->tags)) {
            $tags = collect($this->tags)
                ->map(fn ($tag) => is_string($tag) ? trim($tag) : null)
                ->filter()
                ->unique(fn ($tag) => mb_strtolower($tag))
                ->values()
                ->all();

            $this->merge(['tags' => $tags]);
        }
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Blog title is required',
            'title.unique' => 'Blog title already exists',
            'content.required' => 'Blog content is required',
            'status.required' => 'Status is required',
            'published_at.required_if' => 'Published date is required when status is active',
            'images.max' => 'Maximum 10 images allowed',
            'images.*.image' => 'Only image files are allowed',
            'images.*.mimes' => 'Only JPG, JPEG, PNG and WEBP images are allowed',
            'author_image.image' => 'Only image files are allowed for author image',
            'author_image.mimes' => 'Only JPG, JPEG, PNG and WEBP images are allowed for author image',
            'author_image.max' => 'Author image must not be greater than 2 MB',
        ];
    }
}
