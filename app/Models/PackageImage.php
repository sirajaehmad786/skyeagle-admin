<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PackageImage extends Model
{
    protected $fillable = ['package_id', 'image'];
    protected $appends = ['image_url'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return public_storage_url($this->image);
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($image) {
            if ($image->image && Storage::exists($image->image)) {
                Storage::delete($image->image);
            }
        });
    }
}
