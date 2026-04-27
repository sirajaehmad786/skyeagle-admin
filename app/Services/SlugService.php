<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\Package;

class SlugService
{
    public function generate($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;
        while (Package::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        return $slug;
    }
}