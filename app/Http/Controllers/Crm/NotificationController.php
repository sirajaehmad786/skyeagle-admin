<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Repositories\NotificationRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class NotificationController extends Controller
{
    protected $notificationRepository;
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function readSingle(string $id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();
        if ($notification && !$notification->read_at) {
            $notification->markAsRead();
        }
        return response()->json([
            'success' => true,
            'unread_count' => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    public function readAll(Request $request)
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }
        return back();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->initDataTable($request);
        }
        return view('crm.notification.index');
    }

    public function create()
    {
        //
    }

    public function store(Request $request) {}

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }

    protected function initDataTable($request)
    {
        $data = $this->notificationRepository->initData($request);

        return DataTables::of($data)
           
            ->addColumn(
                'notifiable_type',
                fn($row) =>
                '<div class="w-200px">' . $row->notifiable_type . '</div>'
            )

            ->addColumn('data', function ($row) {
                if (is_array($row->data)) {
                    $data = $row->data;
                } elseif (is_string($row->data)) {
                    $data = json_decode($row->data, true);
                } else {
                    $data = null;
                }

                if (is_array($data)) {
                    $html = '';
                    foreach ($data as $key => $value) {
                        $html .= '<div><strong>'
                            . ucwords(str_replace('_', ' ', $key))
                            . ':</strong> ' . e($value) . '</div>';
                    }
                    return '<div class="w-250px">' . $html . '</div>';
                }
                return '<div class="w-250px">' . e($row->data) . '</div>';
            })
            ->addColumn('read_at', function ($row) {
                return $row->read_at
                    ? '<span class="badge bg-success">Read</span>'
                    : '<span class="badge bg-warning">Unread</span>';
            })

            ->addColumn(
                'created_at',
                fn($row) =>
                formateDate($row->created_at)
            )
            ->rawColumns(['type', 'notifiable_type', 'data','read_at'])
            ->make(true);
    }
}
