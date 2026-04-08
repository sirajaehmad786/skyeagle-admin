<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    protected $table = 'airports';
    protected $fillable = [
        'airport_code',
        'airport_name',
        'city',
        'state_UT',
        'country'
    ];
}
