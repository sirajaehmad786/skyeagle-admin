<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [

        'user_id',
        'module',
        'activity_type',
        'activity_action',
        'reference_type',
        'reference_id',
        'description',
        'old_values',
        'new_values',
        'metadata',
        'ip_address',
        'user_agent',
        'url',
        'method'
    ];

    protected $casts = [

        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}