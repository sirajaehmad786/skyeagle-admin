<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'contact_id',
        'document',  
        'booking_id',      
        'source_type'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

}
