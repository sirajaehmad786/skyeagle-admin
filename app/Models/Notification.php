<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    /**
     * Table name
     */
    protected $table = 'notifications';
    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Polymorphic relation (optional but recommended)
     */
    public function notifiable()
    {
        return $this->morphTo();
    }
}
