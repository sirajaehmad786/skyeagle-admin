<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{

    
    protected $fillable = [
                    'is_transfer',
                    'user_id',
                    'transferred_from_user_id',
                    'lead_code',
                    'contact_id',
                    'query_type',
                    'start_date',
                    'end_date',
                    'no_of_kids',
                    'no_of_adults',
                    'food_preference',
                    'meals',
                    'additional_note',
                    'hotel_category',
                    'customer_category',
                    'visa_requirements',
                    'flight_requirements',
                    'hotel_requirements',
                    'sightseeing_requirements',
                    'flight_from',
                    'flight_to',
                    'company_name',
                    'gst_no',
                    'pan_no',
                    'tags',
                    'remarks',
                    'lead_stage',
                    'lead_status',
                    'travel_type',
                    'destination'
                ];

    public $timestamps = true;

    protected $casts = [
        'destination' => 'array',
    ];

    protected $dates = ['start_date', 'end_date']; // optional in Laravel 12 if you're using casts

    // Laravel 12+ way using accessors and mutators (Attribute class)
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

    /**
     * Lead belongs to one contact.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * User assigned to this lead (source of truth for user association).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assign_by(){
        return $this->belongsTo(User::class, 'id', 'assign_by');
    }
    
    public function quotations(){
        return $this->hasOne(Quotation::class);
    }

    public function quotationsId()
    {
        return $this->hasMany(Quotation::class, 'lead_id');
    }

    public function assignmentHistory()
    {
        return $this->hasMany(LeadHistory::class);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    /**
     * @param  int|null  $userId  User ID for the lead (used for count; from lead or request).
     */
    public static function generateLeadCode($userId)
    {
        $user = User::findOrFail($userId);
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $first = strtoupper(substr(trim($user->first_name ?? ''), 0, 1));
        $last  = strtoupper(substr(trim($user->last_name ?? ''), 0, 1));

        $initial = $first . $last;
        $year = date('y');

        $prefix = config('constant.lead_code_prefix')."{$initial}{$year}";
        $lastLead = Lead::where('user_id', $userId)->latest()->first();
        if(empty($lastLead)){
            $leadCount = 0;
        }else{
            $lastCode = $lastLead->lead_code;
            $lastFourDigits = substr((string) $lastCode, -4);
            $leadCount = ctype_digit($lastFourDigits) ? (int) $lastFourDigits : 0;
        }
        $leadCount = $leadCount + 1;
        $newLeadCount = str_pad($leadCount, 4, '0', STR_PAD_LEFT);
        return $prefix . $newLeadCount;
    }

    public function histories()
    {
        return $this->hasMany(LeadHistory::class)->latest();
    }

}
