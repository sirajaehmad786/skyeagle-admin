<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'parent_id',
        'first_name',
        'last_name',
        'password',
        'profile_image',
        'email',
        'email_verified_at',
        'phone',
        'al_phone',
        'status',
    ];

    public $timestamps = true;
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function scopeActive($query){
        return $query->where('status', 'Active');
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn() => trim("{$this->first_name} {$this->last_name}")
        );
    }

     // Mutator for password
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    public function teamMembers()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function tourBookingRequests()
    {
        return $this->hasMany(TourBookingRequest::class);
    }

    public static function hierarchyUserIdsFor(?User $user = null): array
    {
        $user = $user ?: auth()->user();

        if (! $user) {
            return [];
        }

        $resultIds = [$user->id];
        $currentLevelIds = [$user->id];

        while (! empty($currentLevelIds)) {
            $childrenIds = self::whereIn('parent_id', $currentLevelIds)
                ->pluck('id')
                ->all();

            $childrenIds = array_values(array_diff($childrenIds, $resultIds));

            if (empty($childrenIds)) {
                break;
            }

            $resultIds = array_merge($resultIds, $childrenIds);
            $currentLevelIds = $childrenIds;
        }

        return $resultIds;
    }
}
