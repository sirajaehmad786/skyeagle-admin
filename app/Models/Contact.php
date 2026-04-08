<?php

namespace App\Models;

use App\Constants\ContactStatus;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'initial',
        'first_name',
        'last_name',
        'email',
        'mobile_no',
        'al_mobile_no',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'dob',
        'passport_number',
        'passport_expiry',
        'marital_status',
        'marriage_date',
        'lead_source',
        'tract',
        'status',
    ];

    protected $attributes = [
        'status' => ContactStatus::ACTIVE,
    ];

    public $timestamps = true;

    protected static function booted()
    {
        static::addGlobalScope('teamAccess', function (Builder $builder) {
            $user = auth()->user();

            if ($user && !$user->hasRole(config('constant.super_admin_role'))) {
                $allowedUserIds = User::hierarchyUserIdsFor($user);

                $builder->whereHas('leads', function ($q) use ($allowedUserIds) {
                    $q->whereIn('leads.user_id', $allowedUserIds);
                });
            }
        });
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn() => trim("{$this->initial} {$this->first_name} {$this->last_name}")
        );
    }

    protected function location(): Attribute
    {
        return Attribute::make(
            get: fn() => trim("{$this->city}, {$this->state}, {$this->country}")
        );
    }

    /**
     * One contact can have multiple leads.
     */
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
