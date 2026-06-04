<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerReview extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'review_title',
        'review_description',
        'reviewer_name',
        'reviewer_location',
        'reviewer_image',
        'rating',
        'sort_order',
    ];
}
