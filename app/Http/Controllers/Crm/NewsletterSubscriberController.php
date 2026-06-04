<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Repositories\NewsletterSubscriberRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class NewsletterSubscriberController extends Controller
{
    protected $newsletterSubscriberRepository;

    public function __construct(
        NewsletterSubscriberRepository $newsletterSubscriberRepository
    ) {
        $this->newsletterSubscriberRepository = $newsletterSubscriberRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->initDataTable($request);
        }
        return view('crm.subscriber-newsletter.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    protected function initDataTable($request)
    {
        $data = $this->newsletterSubscriberRepository->initData();
        return DataTables::of($data)
        ->filter(function ($query) use ($request) {
            if ($request->has('search') && $request->search['value']) {
                $search = strtolower($request->search['value']);
                $query->where(function ($q) use ($search) {
                    $q->whereRaw(
                        'LOWER(email) LIKE ?',
                        ["%{$search}%"]
                    );
                });
            }
        })
        ->addColumn('email', function ($row) {
            return $row->email;
        })
        ->addColumn('subscribed_at', function ($row) {
            return $row->subscribed_at
                ? Carbon::parse($row->subscribed_at)
                    ->timezone('Asia/Kolkata')
                    ->format('d-m-Y h:i A')
                : '-';
        })
        ->addColumn('unsubscribed_at', function ($row) {
            return $row->unsubscribed_at
            ? Carbon::parse($row->unsubscribed_at)
                ->timezone('Asia/Kolkata')
                ->format('d-m-Y h:i A')
            : '-';
        })
        ->addColumn('created_at', function ($row) {
            return Carbon::parse($row->created_at)
            ->timezone('Asia/Kolkata')
            ->format('d-m-Y h:i A');
        })
        ->make(true);
    }

}
