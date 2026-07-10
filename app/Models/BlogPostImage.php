<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BlogPostImage extends Model
{
    protected $appends = ['image_url'];

    protected $fillable = [
        'blog_post_id',
        'image',
        'sort_order',
    ];

    public function getImageUrlAttribute(): ?string
    {
        return public_storage_url($this->image);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            if ($image->image && Storage::disk('public')->exists($image->image)) {
                Storage::disk('public')->delete($image->image);
            }
        });
    }

    public function post()
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }
}
