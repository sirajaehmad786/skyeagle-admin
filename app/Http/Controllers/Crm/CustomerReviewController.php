<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\CustomerReview;
use App\Models\Package;
use Illuminate\Http\Request;
use App\Repositories\CustomerReviewRepository;
use Yajra\DataTables\Facades\DataTables;

class CustomerReviewController extends Controller
{
    protected $customerReviewRepository;
    public function __construct(CustomerReviewRepository $customerReviewRepository)
    {
        $this->customerReviewRepository = $customerReviewRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->initDataTable($request);
        }
        return view('crm.customerReview.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $packages = Package::where('status', 1)->orderBy('package_name')->get(['id', 'package_name']);
        return view('crm.customerReview.create', compact('packages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'review_description' => 'required|string',
            'reviewer_name'      => 'required|string|max:255',
            'reviewer_location'  => 'required|string|max:255',
            'rating'             => 'required|numeric|min:1|max:5',
            'package_id'          => 'nullable|exists:packages,id',
        ]);

        $this->customerReviewRepository->saveCustomerReview($request);
        session()->flash('success', 'Customer review created successfully.');

        return response()->json([
            'status'       => true,
            'message'      => 'Customer review created successfully.',
            'redirect_url' => route('customer-review.index'),
        ]);
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
        $review = $this->customerReviewRepository->findById($id);
        $packages = Package::where('status', 1)->orderBy('package_name')->get(['id', 'package_name']);
        return view('crm.customerReview.edit', compact('review', 'packages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'review_description' => 'required|string',
            'reviewer_name'      => 'required|string|max:255',
            'reviewer_location'  => 'required|string|max:255',
            'rating'             => 'required|numeric|min:1|max:5',
            'package_id'          => 'nullable|exists:packages,id',
        ]);

        $this->customerReviewRepository->updateReview($request, $id);
        session()->flash('success', 'Customer review updated successfully.');

        return response()->json([
            'status'       => true,
            'message'      => 'Customer review updated successfully.',
            'redirect_url' => route('customer-review.index'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->customerReviewRepository->deleteReview($id);
        return response()->json([
            'status' => true,
            'message' => 'Customer Review deleted successfully.'
        ]);
    }

    protected function initDataTable($request)
    {
        $data = $this->customerReviewRepository->initData($request);

        return DataTables::of($data)
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && $request->search['value']) {
                    $search = strtolower($request->search['value']);

                    $query->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(reviewer_name) LIKE ?', ["%{$search}%"])
                          ->orWhereRaw('LOWER(reviewer_location) LIKE ?', ["%{$search}%"])
                          ->orWhereHas('package', function ($packageQuery) use ($search) {
                              $packageQuery->whereRaw('LOWER(package_name) LIKE ?', ["%{$search}%"]);
                          });
                    });
                }
            })
            ->addColumn('package_name', function ($row) {
                return $row->package?->package_name ?? 'General';
            })
            ->addColumn('reviewer_name', function ($row) {
                return $row->reviewer_name;
            })
            ->addColumn('reviewer_location', function ($row) {
                return $row->reviewer_location ?? '-';
            })
            ->addColumn('rating', function ($row) {
                return $row->rating;
            })
            ->addColumn('created_at', function ($row) {
                return formateDate($row->created_at);
            })
            ->addColumn('action', function ($row) {
                return view('crm.customerReview.action', compact('row'))->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
