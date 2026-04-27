<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageHighlight extends Model
{
    protected $fillable = [
        'package_id',
        'highlight'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    
}
