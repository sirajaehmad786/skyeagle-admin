<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class QuotationVisa extends Model
{
    protected $fillable = [
                "lead_id",
                "quotation_id",
                "visa_country",
                "visa_category",
                "visa_travel_date",
                "visa_adults",
                "visa_child",
                "visa_infant",
                "visa_adult_price",
                "visa_child_price",
                "visa_type",
                "visa_remarks"
            ];

    protected $appends = ['price'];

    public $timestamps = true;
    
    public function quotation(){
        return $this->belongsTo(Quotation::class);
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => 
                (floatval($attributes['visa_adult_price'] ?? 0) * floatval($attributes['visa_adults'] ?? 0)) + 
                (floatval($attributes['visa_child_price'] ?? 0) * floatval($attributes['visa_child'] ?? 0))
        );
    }
    
}
