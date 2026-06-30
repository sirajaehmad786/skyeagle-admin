<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class CustomerReview extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'package_id',
        'review_title',
        'review_description',
        'reviewer_name',
        'reviewer_location',
        'reviewer_image',
        'rating',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'rating' => 'float',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected static function booted()
    {
        static::deleting(function ($review) {
            if ($review->reviewer_image && Storage::disk('public')->exists($review->reviewer_image)) {
                Storage::disk('public')->delete($review->reviewer_image);
            }
        });
    }
}
