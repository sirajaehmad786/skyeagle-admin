<?php

namespace App\Repositories;

use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BlogPostRepository extends BaseRepository
{
    public function __construct(BlogPost $blogPost)
    {
        parent::__construct($blogPost);
    }

    public function initData($request = null)
    {
        $query = BlogPost::query()
            ->with(['category', 'tags'])
            ->latest();

        if ($request) {
            $this->applyFilters($query, $request);
        }

        return $query;
    }

    public function getById($id)
    {
        return BlogPost::with(['category', 'tags', 'images', 'comments'])->findOrFail($id);
    }

    public function createBlogPost($request)
    {
        return DB::transaction(function () use ($request) {
            $data = $this->prepareData($request);
            $data['user_id'] = Auth::id();
            $post = $this->model->create($data);
            $this->syncTags($post, $request->tags ?? []);
            $this->uploadBlogImages($request, $post->id);
            $this->refreshFeaturedImage($post);

            return $post;
        });
    }

    public function updateBlogPost($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $post = $this->model->findOrFail($id);
            $post->update($this->prepareData($request));
            $this->syncTags($post, $request->tags ?? []);
            $this->deleteRemovedImages($request);
            $this->uploadBlogImages($request, $post->id);
            $this->refreshFeaturedImage($post);

            return $post;
        });
    }

    public function inlineUpdate($id, array $data)
    {
        $field = $data['field'] ?? null;
        $value = $data['value'] ?? null;

        $rules = match ($field) {
            'title' => ['value' => ['required', 'string', 'max:255', Rule::unique('blog_posts', 'title')->ignore($id)]],
            'category_id' => ['value' => ['nullable', 'exists:categories,id']],
            'status' => ['value' => ['required', Rule::in(config('constant.status', []))]],
            default => null,
        };

        if (!$rules) {
            abort(422, 'Invalid editable field.');
        }

        Validator::make(['value' => $value], $rules)->validate();

        $post = $this->model->findOrFail($id);
        $post->{$field} = $value ?: null;

        if ($field === 'status' && $value === config('constant.status.0', 'Active') && !$post->published_at) {
            $post->published_at = now();
        }

        $post->save();

        return $post;
    }

    protected function applyFilters($query, $request): void
    {
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('tag_id')) {
            $query->whereHas('tags', fn ($tagQuery) => $tagQuery->where('blog_tags.id', $request->tag_id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', (int) $request->is_featured);
        }

        if ($request->filled('published_from')) {
            $query->where('published_at', '>=', istDateRangeToUtc($request->published_from));
        }

        if ($request->filled('published_to')) {
            $query->where('published_at', '<=', istDateRangeToUtc($request->published_to, true));
        }

        if ($request->filled('created_from')) {
            $query->where('created_at', '>=', istDateRangeToUtc($request->created_from));
        }

        if ($request->filled('created_to')) {
            $query->where('created_at', '<=', istDateRangeToUtc($request->created_to, true));
        }
    }

    protected function prepareData($request): array
    {
        $content = $request->content ?? '';
        $readingTime = $request->reading_time_minutes ?: $this->calculateReadingTime($content);

        return [
            'category_id' => $request->category_id,
            'title' => $request->title,
            'excerpt' => $request->excerpt,
            'content' => $content,
            'status' => $request->status,
            'is_featured' => $request->has('is_featured') ? 1 : 0,
            'published_at' => $request->published_at,
            'reading_time_minutes' => $readingTime,
        ];
    }

    protected function calculateReadingTime(string $content): int
    {
        $words = str_word_count(strip_tags($content));
        return max(1, (int) ceil($words / 200));
    }

    protected function syncTags(BlogPost $post, array $tags): void
    {
        $names = collect($tags)
            ->map(fn ($tag) => is_string($tag) ? trim($tag) : null)
            ->filter()
            ->unique(fn ($name) => mb_strtolower($name))
            ->values();

        $sync = [];
        foreach ($names as $name) {
            $tag = BlogTag::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first()
                ?? BlogTag::create(['name' => $name, 'status' => 1]);

            $sync[$tag->id] = ['created_at' => now()];
        }

        $post->tags()->sync($sync);
    }

    public function uploadBlogImages($request, $postId): void
    {
        if (!$request->hasFile('images')) {
            return;
        }

        $files = $request->file('images');
        if (!is_array($files)) {
            $files = [$files];
        }

        $existingCount = DB::table('blog_post_images')->where('blog_post_id', $postId)->count();
        if (($existingCount + count($files)) > 10) {
            throw new \Exception('Maximum 10 images allowed');
        }

        foreach ($files as $file) {
            if (!$file->isValid()) {
                continue;
            }

            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('blogs', $fileName, 'public');

            DB::table('blog_post_images')->insert([
                'blog_post_id' => $postId,
                'image' => $path,
                'sort_order' => $existingCount++,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function deleteRemovedImages($request): void
    {
        if (!$request->removed_images) {
            return;
        }

        $images = json_decode($request->removed_images, true);
        if (!is_array($images)) {
            return;
        }

        foreach ($images as $image) {
            if (!isset($image['id'], $image['path'])) {
                continue;
            }

            if (Storage::disk('public')->exists($image['path'])) {
                Storage::disk('public')->delete($image['path']);
            }

            DB::table('blog_post_images')->where('id', $image['id'])->delete();
        }
    }

    protected function refreshFeaturedImage(BlogPost $post): void
    {
        $firstImage = $post->images()->orderBy('sort_order')->orderBy('id')->first();
        $post->forceFill([
            'featured_image' => $firstImage?->image,
        ])->save();
    }

    public function delete($id)
    {
        $post = BlogPost::with('images')->findOrFail($id);
        return $post->delete();
    }
}
