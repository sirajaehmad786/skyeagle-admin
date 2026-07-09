<?php
namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Repositories\EnquiryRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class EnquiryController extends Controller
{

    protected $enquiryRepository;
    public function __construct(EnquiryRepository $enquiryRepository)
    {
        $this->enquiryRepository = $enquiryRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->initDataTable($request);
        }

        $sources = $this->enquiryRepository->sources();

        return view('crm.enquiry.index', compact('sources'));
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
        $data = $this->enquiryRepository->initData($request);
        return DataTables::of($data)
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && $request->search['value']) {
                    $search = strtolower($request->search['value']);
                    $query->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(message) LIKE ?', ["%{$search}%"]);
                    });
                }
            })
            ->addColumn('name', function ($row) {
                return '<div class="w-150px">' . ($row->name ?? '-') . '</div>';
            })
            ->addColumn('email', function ($row) {
                return '<div class="w-150px">' . ($row->email ?? '-') . '</div>';
            })
            ->addColumn('phone', function ($row) {
                return '<div class="w-150px">' . ($row->phone ?? '-') . '</div>';
            })
            ->addColumn('message', function ($row) {
                $fullMessage = $row->message ?? '-';
                $shortMessage = Str::limit($fullMessage, 50, '........');

                return '
                    <div class="w-150px message-cell" 
                        data-full="'.e($fullMessage).'" 
                        style="cursor:pointer;">
                        '.$shortMessage.'
                    </div>
                ';
            })
            ->addColumn('source', function ($row) {
                if ($row->source === 'contact') {
                    return '<span class="badge bg-info-subtle text-info border border-info">
                                <i class="ri-customer-service-2-line me-1"></i> Contact
                            </span>';
                } elseif ($row->source === 'tour-details') {
                    return '<span class="badge bg-warning-subtle text-warning border border-warning">
                                <i class="ri-map-pin-line me-1"></i> Tour Details
                            </span>';
                } else {
                    return '<span class="badge bg-secondary">-</span>';
                }
            })
            ->addColumn('ip_address', function ($row) {
                return '<div class="w-150px">' . ($row->ip_address ?? '-') . '</div>';
            })
            ->addColumn('created_at', function ($row) {
                return formateDate($row->created_at);
            })
            ->rawColumns([
                'name',
                'email',
                'phone',
                'message',
                'source',
                'ip_address',
                'created_at'
            ])
            ->make(true);
    }
}
