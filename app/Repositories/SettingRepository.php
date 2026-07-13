<?php

namespace App\Repositories;

use App\Models\Setting;
use Illuminate\Support\Collection;

class SettingRepository
{
    private const KEYS = ['description', 'visa_policy', 'payment_policy'];

    public function storeOrUpdate($request): void
    {
        foreach (self::KEYS as $key) {
            Setting::setValue($key, $this->sanitizeContent($request->$key ?? ''));
        }
    }

    public function getSettings(): Collection
    {
        return Setting::whereIn('key', self::KEYS)->pluck('value', 'key');
    }

    private function sanitizeContent($value): string
    {
        if (is_array($value)) {
            $value = $value[0] ?? '';
        }
        if ($this->isBase64($value)) {
            $value = base64_decode($value) ?: $value;
        }
        return (string) $value;
    }

    private function isBase64($string): bool
    {
        return base64_encode(base64_decode($string, true)) === $string;
    }
}
