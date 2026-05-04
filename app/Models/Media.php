<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'id',
        'module',
        'section',
        'module_id',
        'title',
    	'sub_title',
        'button_text',
        'redirect_url',
        'start_date',
        'end_date',
        'sort_order',
        'is_active'
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($media) {
            $media->load('images');
            foreach ($media->images as $image) {
                if ($image->file_path && Storage::exists($image->file_path)) {
                    Storage::delete($image->file_path);
                }
                $image->delete();
            }
        });
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = $value
            ? Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d')
            : null;
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = $value
            ? Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d')
            : null;
    }
    
    public function images()
    {
        return $this->hasMany(MediaImage::class);
    }
}
