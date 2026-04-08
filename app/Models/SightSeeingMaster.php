<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SightSeeingMaster extends Model
{
    use HasFactory;

    // Define the table name (since it’s not plural by default)
    protected $table = 'sight_seeing_master';

    // Mass assignable fields
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'images',
    ];

    /**
     * Relationship: A sight seeing record belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','id');
    }
}
