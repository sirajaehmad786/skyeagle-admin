<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'booking_id',
        'quotation_id',
        'user_id',
    ];

    /** Start date from quotation (travel start). */
    protected function startDate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quotation?->start_date,
        );
    }

    /** End date from quotation (travel end). */
    protected function endDate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quotation?->end_date,
        );
    }

    public function contact()
    {
        return $this->hasOneThrough(
            Contact::class,
            Quotation::class,
            'id',          // Foreign key on quotations table (quotations.id)
            'id',          // Foreign key on contacts table (contacts.id)
            'quotation_id',// Local key on bookings table
            'contact_id'   // Local key on quotations table
        );
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'id')
            ->withoutGlobalScope(\App\Scopes\UserQuotationScope::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function payment(){
        return $this->hasMany(Payment::class, 'booking_id', 'id');
    }
    public function booking()
    {
        return $this->hasOne(Booking::class, 'quotation_id');
    }
}
