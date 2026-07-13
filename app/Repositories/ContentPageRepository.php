<?php

namespace App\Repositories;

use App\Models\ContentPage;
use Illuminate\Support\Collection;

class ContentPageRepository
{
    public function getManagedPages(): Collection
    {
        $pages = ContentPage::query()
            ->whereIn('slug', array_keys(ContentPage::MANAGED_PAGES))
            ->orderBy('sort_order')
            ->get()
            ->keyBy('slug');

        foreach (ContentPage::MANAGED_PAGES as $slug => $title) {
            if ($pages->has($slug)) {
                continue;
            }

            $pages->put($slug, ContentPage::create([
                'title' => $title,
                'slug' => $slug,
                'content' => null,
                'is_active' => true,
                'sort_order' => $this->sortOrderForSlug($slug),
            ]));
        }

        return collect(array_keys(ContentPage::MANAGED_PAGES))
            ->mapWithKeys(fn ($slug) => [$slug => $pages->get($slug)]);
    }

    public function saveManagedPages(array $payload): Collection
    {
        $savedPages = collect();

        foreach (ContentPage::MANAGED_PAGES as $slug => $title) {
            $data = $payload[$slug] ?? [];
            $page = ContentPage::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $data['title'] ?? $title,
                    'content' => $this->sanitizeContent($data['content'] ?? null),
                    'is_active' => isset($data['is_active']),
                    'sort_order' => $this->sortOrderForSlug($slug),
                ]
            );

            $savedPages->put($slug, $page);
        }

        return $savedPages;
    }

    private function sortOrderForSlug(string $slug): int
    {
        return array_search($slug, array_keys(ContentPage::MANAGED_PAGES), true) + 1;
    }

    private function sanitizeContent(?string $value): ?string
    {
        $value = trim($this->normalizeStoredHtml((string) $value));

        if ($value === '' || $value === '<p><br></p>') {
            return null;
        }

        return $value;
    }

    private function normalizeStoredHtml(string $value): string
    {
        for ($i = 0; $i < 3; $i++) {
            $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            if ($decoded === $value) {
                break;
            }

            $value = $decoded;
        }

        return $value;
    }
}
