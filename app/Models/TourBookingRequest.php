<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourBookingRequest extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'package_id',
        'user_id',
        'name',
        'email',
        'phone',
        'travel_from_date',
        'travel_to_date',
        'adults',
        'children',
        'infants',
        'special_request',
        'estimated_price',
        'currency',
        'package_name_snapshot',
        'package_code_snapshot',
        'package_price_snapshot',
        'status',
        'admin_note',
        'ip_address',
        'source',
    ];

    protected $casts = [
        'travel_from_date' => 'date',
        'travel_to_date' => 'date',
        'estimated_price' => 'decimal:2',
        'package_price_snapshot' => 'decimal:2',
        'adults' => 'integer',
        'children' => 'integer',
        'infants' => 'integer',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_CONTACTED,
            self::STATUS_CONFIRMED,
            self::STATUS_CANCELLED,
        ];
    }

    public static function statusOptions(): array
    {
        return config('constant.tour_booking_request_status', [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONTACTED => 'Contacted',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_CANCELLED => 'Cancelled',
        ]);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
