<?php

namespace App\Repositories;

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

        if ($request->filled('notifiable_type')) {
            $query->where('notifiable_type', $request->notifiable_type);
        }

        if ($request->filled('read_status')) {
            if ($request->read_status === 'read') {
                $query->whereNotNull('read_at');
            }

            if ($request->read_status === 'unread') {
                $query->whereNull('read_at');
            }
        }

        if ($request->filled('created_from')) {
            $query->where('created_at', '>=', istDateRangeToUtc($request->created_from));
        }

        if ($request->filled('created_to')) {
            $query->where('created_at', '<=', istDateRangeToUtc($request->created_to, true));
        }

        if ($request->filled('search_text')) {
            $search = strtolower($request->search_text);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(notifiable_type) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(data) LIKE ?', ["%{$search}%"]);
            });
        }

        return $query->latest();
    }

    public function notifiableTypes()
    {
        return Notification::query()
            ->whereNotNull('notifiable_type')
            ->where('notifiable_type', '!=', '')
            ->distinct()
            ->orderBy('notifiable_type')
            ->pluck('notifiable_type');
    }
    
}
