<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentPage extends Model
{
    public const PRIVACY_POLICY = 'privacy-policy';
    public const TERMS_CONDITIONS = 'terms-conditions';
    public const REFUND_POLICY = 'refund-policy';

    public const MANAGED_PAGES = [
        self::PRIVACY_POLICY => 'Privacy Policy',
        self::TERMS_CONDITIONS => 'Terms and Conditions',
        self::REFUND_POLICY => 'Refund Policy',
    ];

    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
