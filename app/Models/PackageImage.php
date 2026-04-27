<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PackageImage extends Model
{
    protected $fillable = ['package_id', 'image'];
    public function package()
    {
        return $this->belongsTo(Package::class);
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
