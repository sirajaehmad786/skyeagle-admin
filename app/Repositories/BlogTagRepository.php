<?php

namespace App\Repositories;

use App\Models\BlogTag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BlogTagRepository extends BaseRepository
{
    public function __construct(BlogTag $blogTag)
    {
        parent::__construct($blogTag);
    }

    public function createTag(array $data)
    {
        return $this->model->create([
            'name' => $data['name'],
            'status' => $data['status'],
        ]);
    }

    public function updateTag($id, array $data)
    {
        $tag = $this->model->findOrFail($id);
        $tag->update(array_filter([
            'name' => $data['name'] ?? null,
            'status' => $data['status'] ?? null,
        ], fn ($value) => $value !== null));

        return $tag;
    }

    public function initData($request = null)
    {
        $query = BlogTag::query()->latest();

        if ($request) {
            $this->applyFilters($query, $request);
        }

        return $query;
    }

    public function inlineUpdate($id, array $data)
    {
        $field = $data['field'] ?? null;
        $value = $data['value'] ?? null;

        $rules = match ($field) {
            'name' => ['value' => ['required', 'string', 'max:100', Rule::unique('blog_tags', 'name')->ignore($id)]],
            'status' => ['value' => ['required', Rule::in([0, 1])]],
            default => null,
        };

        if (!$rules) {
            abort(422, 'Invalid editable field.');
        }

        Validator::make(['value' => $value], $rules)->validate();

        return $this->updateTag($id, [$field => $value]);
    }

    protected function applyFilters($query, $request): void
    {
        if ($request->filled('status')) {
            $query->where('status', (int) $request->status);
        }

        if ($request->filled('created_from')) {
            $query->where('created_at', '>=', istDateRangeToUtc($request->created_from));
        }

        if ($request->filled('created_to')) {
            $query->where('created_at', '<=', istDateRangeToUtc($request->created_to, true));
        }
    }

    public function active()
    {
        return BlogTag::query()->where('status', 1)->orderBy('name')->get();
    }

    public function delete($id)
    {
        $tag = BlogTag::findOrFail($id);
        $tag->posts()->detach();
        return $tag->delete();
    }
}
