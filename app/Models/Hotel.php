<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use SoftDeletes;
    use HasFactory;
    
    protected $fillable = [
        'name',
        'address',
        'images',
        'state_id',
        'city_id'
    ];

    protected $casts = [
        'images' => 'string'
    ];

    /**
     * Mutator to capitalize the first letter of the hotel name.
     * This will automatically capitalize the first letter whenever the name is set.
     */
    public function setNameAttribute($value)
    {
        if ($value) {
            $trimmed = trim($value);
            // Capitalize only the first character, keep the rest as entered
            $this->attributes['name'] = ucfirst($trimmed);
        } else {
            $this->attributes['name'] = $value;
        }
    }
}
