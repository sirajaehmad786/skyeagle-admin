<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class QuotationHotel extends Model
{
    protected $fillable = [
        'lead_id',
        'quotation_id',
        'hotel_id',
        'check_in',
        'check_out',
        'total_room',
        'single_room',
        'triple_room',
        'total_cwb',
        'triple_room_price',
        'total_room_price',
        'total_adult',
        'single_room_price',
        'room_type',
        'destination',
        'total_cnb',
        'total_cwb_price',
        'total_cnb_price',
        
        'hotel_remarks',
        'meals',
    ];

    /**
     * Hotel price:
     * - double room price & triple room price are stored as totals for their room counts
     * - single/cnb/cwb prices are stored as per-person prices, so we multiply by their counts
     */
    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn () => 
                  (float) ($this->single_room_price ?? 0) * (int) ($this->single_room ?? 0)
                + (float) ($this->total_room_price ?? 0) * (int) ($this->total_room ?? 0) //double room
                + (float) ($this->triple_room_price ?? 0) * (int) ($this->triple_room ?? 0)
                + (float) ($this->total_cnb_price ?? 0) * (int) ($this->total_cnb ?? 0)
                + (float) ($this->total_cwb_price ?? 0) * (int) ($this->total_cwb ?? 0)
        );
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'id');
    }

    protected function checkIn(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value 
                ? Carbon::parse($value)->format(config('constant.datetime_format', 'd-m-Y H:i')) 
                : null,
            set: fn($value) => $value 
                ? Carbon::createFromFormat('d-m-Y H:i', $value)->format('Y-m-d H:i:s') 
                : null,
        );
    }

    protected function checkOut(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value 
                ? Carbon::parse($value)->format(config('constant.datetime_format', 'd-m-Y H:i')) 
                : null,
            set: fn($value) => $value 
                ? Carbon::createFromFormat('d-m-Y H:i', $value)->format('Y-m-d H:i:s') 
                : null,
        );
    }

    protected function nights(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->attributes['check_in'] || !$this->attributes['check_out']) {
                    return 0;
                }
                $checkIn = Carbon::parse($this->attributes['check_in']);
                $checkOut = Carbon::parse($this->attributes['check_out']);
                $checkInDate = $checkIn->copy()->startOfDay();
                $checkOutDate = $checkOut->copy()->startOfDay();
                if ($checkOutDate->lt($checkInDate)) {
                    return 0;
                }
                return (int) $checkInDate->diffInDays($checkOutDate);
            }
        );
    }
}
