<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaImage extends Model
{
    use HasFactory;

    protected $table = 'media_images';

    protected $fillable = [
        'media_id',
        'file_name',
        'file_path',
        'file_type',
        'sort_order',
        'is_active',
    ];

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }
}
