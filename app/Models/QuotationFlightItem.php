<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class QuotationFlightItem extends Model
{

    protected $fillable = [
        "flight_id",
        "quotation_id",
        "from_city",
        "to_city",
        "date",
    ];

    public $timestamps = true;

    public function flight(){
        return $this->belongsTo(QuotationFlight::class, 'id', 'flight_id');
    }

    public function fromAirport()
    {
        return $this->belongsTo(Airport::class, 'from_city');
    }

    public function toAirport()
    {
        return $this->belongsTo(Airport::class, 'to_city');
    }

    protected function date(): Attribute
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
}