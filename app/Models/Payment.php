<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['booking_id','payment_id','user_id','amount','payment_method','image','payment_date','status','remarks'];
    public $timestamps = true;
    protected $dates = ['payment_date'];

    public function booking(){
        return $this->belongsTo(Booking::class,'booking_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}