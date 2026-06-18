<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PackageAttribute extends Model
{
    use SoftDeletes;

    public const TYPE_POPULAR = 'popular';
    public const TYPE_ACCOMMODATION = 'accommodation';
    public const TYPE_ACTIVITY = 'activity';
    public const TYPE_MEAL_PLAN = 'meal_plan';

    protected $fillable = [
        'type',
        'name',
        'slug',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (PackageAttribute $attribute) {
            $attribute->slug = Str::slug($attribute->name);
        });
    }

    public static function typeOptions(): array
    {
        $types = config('constant.package_attribute_types', []);

        try {
            if (Schema::hasTable('package_attributes')) {
                static::query()
                    ->select('type')
                    ->whereNotNull('type')
                    ->distinct()
                    ->orderBy('type')
                    ->pluck('type')
                    ->each(function ($type) use (&$types) {
                        $types[$type] = $types[$type] ?? static::typeLabel($type);
                    });
            }
        } catch (\Throwable $exception) {
            return $types;
        }

        return $types;
    }

    public static function typeLabel(?string $type): string
    {
        if (blank($type)) {
            return '';
        }

        $configuredTypes = config('constant.package_attribute_types', []);

        return $configuredTypes[$type] ?? Str::of($type)
            ->replace(['-', '_'], ' ')
            ->title()
            ->toString();
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_attribute_package')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
