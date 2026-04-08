<?php

namespace App\Models;

use App\Scopes\UserQuotationScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{

    protected $fillable = [
        'user_id',
        'contact_id',
        'lead_id',
        'start_date',
        'end_date',
        'company_id',
        'inclusion',
        'exclusion',
        'amount_description_services',
        'visa_adult_service_charge',
        'visa_child_service_charge',
        'double_room_service_price',
        'triple_room_service_price',
        'single_room_service_price',
        'total_cnb_service_price',
        'sightseeing_adult_service_charge',
        'sightseeing_child_service_charge',
        'sightseeing_adult_price',
        'sightseeing_child_price',
        'discount'
    ];

    protected $casts = [
        'amount_description_services' => 'array',
    ];

    public $timestamps = true;

    protected static function booted()
    {
        static::addGlobalScope(new UserQuotationScope);
    }

    public function flight()
    {
        return $this->hasOne(QuotationFlight::class, 'quotation_id', 'id');
    }

    public function visa()
    {
        return $this->hasMany(QuotationVisa::class);
    }

    public function sightseeing()
    {
        return $this->hasMany(QuotationSight::class, 'quotation_id', 'id')->with('items')->orderBy('id', 'ASC');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function lead()
    {
        return $this->hasOne(Lead::class, 'id', 'lead_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hotel()
    {
        return $this->hasMany(QuotationHotel::class, 'quotation_id', 'id')->orderBy('id', 'asc');
    }

    protected function startDate(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->format(config('constant.date_format')),
            set: fn($value) => Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d')
        );
    }

    protected function endDate(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->format(config('constant.date_format')),
            set: fn($value) => Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d')
        );
    }

    public function booking()
    {
        return $this->hasOne(Booking::class, 'quotation_id', 'id');
    }
    public function leadBooking()
    {
        return $this->hasOneThrough(
            Booking::class,
            Quotation::class,
            'lead_id',      // Foreign key on quotations table
            'quotation_id', // Foreign key on bookings table
            'lead_id',      // Local key on current quotation
            'id'            // Local key on quotations
        );
    }


    /**
     * Total amount from quotation: flight + visa + hotel + sightseeing.
     * Ensure quotation is loaded with: flight, visa, hotel when using this.
     */

    public function getFlightTotalAttribute(): float
    {
        $flight = $this->relationLoaded('flight')
            ? $this->flight
            : $this->flight()->first();
        if (!$flight) {
            return 0;
        }
        $adultCount = (int) ($flight->flight_adults ?? 0);
        $childCount = (int) ($flight->flight_child ?? 0);
        $infantCount = (int) ($flight->flight_infant ?? 0);

        $serviceTotal =
            (float) ($flight->service_price_adult ?? 0) * $adultCount +
            (float) ($flight->service_price_child ?? 0) * $childCount +
            (float) ($flight->service_price_infant ?? 0) * $infantCount;

        return (float) $flight->price + $serviceTotal;
    }

    public function getVisaTotalAttribute(): float
    {
        $visaRows = $this->relationLoaded('visa')
            ? $this->visa
            : $this->visa()->get([
                'visa_adults',
                'visa_child',
                'visa_adult_price',
                'visa_child_price',
            ]);

        $visaBase = (float) $visaRows->sum(fn ($v) => (float) $v->price);

        $serviceTotal =
            ((float) ($this->visa_adult_service_charge ?? 0) * (int) $visaRows->sum('visa_adults')) +
            ((float) ($this->visa_child_service_charge ?? 0) * (int) $visaRows->sum('visa_child'));

        return $visaBase + $serviceTotal;
    }

    public function getHotelTotalAttribute(): float
    {
        $hotelRows = $this->relationLoaded('hotel')
            ? $this->hotel
            : $this->hotel()->get([
                'single_room',
                'total_room',
                'triple_room',
                'total_cnb',
                'single_room_price',
                'total_room_price',
                'triple_room_price',
                'total_cnb_price',
                'total_cwb',
                'total_cwb_price',
            ]);

        // Base hotel amount should come from QuotationHotel::price accessor.
        $hotelBase = (float) $hotelRows->sum(fn ($h) => (float) $h->price);

        // Service charges are per person, so multiply each service rate by its total pax count.
        $serviceTotal =
            (float) ($this->single_room_service_price ?? 0) * (int) $hotelRows->sum('single_room') +
            (float) ($this->double_room_service_price ?? 0) * (int) $hotelRows->sum('total_room') +
            (float) ($this->triple_room_service_price ?? 0) * (int) $hotelRows->sum('triple_room') +
            (float) ($this->total_cnb_service_price ?? 0) * (int) $hotelRows->sum('total_cnb') +
            (float) ($this->total_cnb_service_price ?? 0) * (int) $hotelRows->sum('total_cwb');

        return $hotelBase + $serviceTotal;
    }

    public function getSightseeingTotalAttribute(): float
    {
        $lead = $this->relationLoaded('lead')
            ? $this->lead
            : $this->lead()->first(['no_of_adults', 'no_of_kids']);
        $adults = max(0, (int) ($lead?->no_of_adults ?? 0));
        $kids = max(0, (int) ($lead?->no_of_kids ?? 0));

        $ap = (float) ($this->sightseeing_adult_price ?? 0);
        $cp = (float) ($this->sightseeing_child_price ?? 0);
        $as = (float) ($this->sightseeing_adult_service_charge ?? 0);
        $cs = (float) ($this->sightseeing_child_service_charge ?? 0);

        return $ap * $adults + $cp * $kids + $as * $adults + $cs * $kids;
    }

    public function getTotalAmountAttribute(): float
    {
        $quotationLead = $this->relationLoaded('lead')
            ? $this->lead
            : $this->lead()->first([
                'visa_requirements',
                'flight_requirements',
                'hotel_requirements',
                'sightseeing_requirements',
            ]);
        
        $subtotal = 0;
        if ($quotationLead->flight_requirements == 'Yes') {
            $subtotal += $this->flight_total;
        }
        if ($quotationLead->visa_requirements == 'Yes') {
            $subtotal += $this->visa_total;
        }
        if ($quotationLead->hotel_requirements == 'Yes') {
            $subtotal += $this->hotel_total;
        }
        if ($quotationLead->sightseeing_requirements == 'Yes') {
            $subtotal += $this->sightseeing_total;
        }
        return $subtotal - (float) ($this->discount ?? 0);
    }
}
