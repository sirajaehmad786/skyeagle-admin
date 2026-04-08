<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationRepository extends BaseRepository
{
    public function __construct(Notification $notification)
    {
        parent::__construct($notification);
    }

    public function initData($request)
    {
        $query = Notification::query()
            ->select([
               
                'notifiable_type',
                
                'data',
                'read_at',
                'created_at',
            ]);

        return $query->latest();
    }
    
}
