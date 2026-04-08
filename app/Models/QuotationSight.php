<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class QuotationSight extends Model
{
    protected $fillable = [
        'lead_id',
        'quotation_id',
        'day_no',
        'date'
    ];

    public $timestamps = true;

    public function items(){
        return $this->hasMany(QuotationSightItem::class, 'quotation_sight_id', 'id')->orderBy('id');
    }
    
    // Boot method to hook into model events
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($sightseeing) {
            // Delete related items
            foreach ($sightseeing->items as $item) {
                Storage::disk('public')->delete($item->image);
                $item->delete();
            }
        });
    }
    protected function date(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->format(config('constant.date_format')),
            set: fn($value) => Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d')
        );
    }
}
