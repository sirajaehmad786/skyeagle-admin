<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class QuotationSightItem extends Model
{
    protected $fillable = [
                "quotation_sight_id",
                "title",
                "image",
                "description",
            ];

    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($item) {
            // Delete image from storage if exists
            if ($item->image && Storage::disk('public')->exists($item->image)) {
                Storage::disk('public')->delete($item->image);
            }
        });
    }
}
