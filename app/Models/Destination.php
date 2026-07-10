<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Destination extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'country',
        'city',
        'banner_image',
        'description',
        'best_time_to_visit',
        'popular_attractions',
        'faqs',
        'status',
        'created_by',
    ];

    protected $casts = [
        'popular_attractions' => 'array',
        'faqs' => 'array',
        'status' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Destination $destination) {
            if (blank($destination->slug) && filled($destination->name)) {
                $destination->slug = static::uniqueSlug($destination->name, $destination->id);
            }
        });
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class, 'destination_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function getBannerImageUrlAttribute(): ?string
    {
        if (blank($this->banner_image)) {
            return null;
        }

        return public_storage_url($this->banner_image);
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }
}
