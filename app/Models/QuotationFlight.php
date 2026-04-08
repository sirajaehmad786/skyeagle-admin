<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class QuotationFlight extends Model
{
    protected $fillable = [
                "lead_id",
                "quotation_id",
                "travel_mode",
                "trip_type",
                "flight_source_city",
                "flight_destination_city",
                "flight_start_date",
                "flight_end_date",
                "flight_adults",
                "flight_child",
                "flight_infant",
                "adult_price",
                "child_price",
                "infant_price",
                'service_price_adult',
                'service_price_child',
                'service_price_infant',
                "flight_class",
                "flight_remarks"
            ];

    protected $appends = ['price'];

    public $timestamps = true;
    

    public function quotation(){
        return $this->belongsTo(Quotation::class, 'id', 'quotation_id');
    }
    
    public function items(){
        return $this->hasMany(QuotationFlightItem::class, 'flight_id', 'id')->orderBy('id', 'ASC');
    }

    public function sourceAirport()
    {
        return $this->belongsTo(Airport::class, 'flight_source_city');
    }

    public function destinationAirport()
    {
        return $this->belongsTo(Airport::class, 'flight_destination_city');
    }
    
    protected function flightStartDate(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value 
                ? Carbon::parse($value)->format(config('constant.date_format')) 
                : null,
            set: fn($value) => $value 
                ? Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d') 
                : null,
        );
    }

    protected function flightEndDate(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value 
                ? Carbon::parse($value)->format(config('constant.date_format')) 
                : null,
            set: fn($value) => $value 
                ? Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d') 
                : null,
        );
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => 
                (floatval($attributes['adult_price'] ? ($attributes['adult_price'] * $attributes['flight_adults']) : 0) + 
                 floatval($attributes['child_price'] ? ($attributes['child_price'] * $attributes['flight_child']) : 0) + 
                 floatval($attributes['infant_price'] ? ($attributes['infant_price'] * $attributes['flight_infant']) : 0))
        );
    }
}
