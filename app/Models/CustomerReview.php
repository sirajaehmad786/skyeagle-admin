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
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
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
