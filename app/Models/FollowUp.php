<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    
    protected $fillable = [
        'lead_id',
        'user_id',
        'follow_up_date',
        'follow_up_time',
        'lead_stage',
        'lead_status',
        'remarks',
        'is_notified'
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
