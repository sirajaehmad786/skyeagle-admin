<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\PackageRequest;
use App\Models\Category;
use App\Models\Destination;
use App\Models\PackageAttribute;
use App\Repositories\PackageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
class PackageController extends Controller
{
    protected $packageRepository;

    public function __construct(PackageRepository $packageRepository)
    {
        $this->packageRepository = $packageRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->initDataTable($request);
        }
        return view('crm.package.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $destinations = Destination::active()->orderBy('name')->get();
        $packageAttributes = PackageAttribute::active()
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('type');

        return view('crm.package.create', compact('categories', 'packageAttributes', 'destinations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $package = $this->packageRepository->createPackage($request);
            if ($request->hasFile('images')) {
                $this->packageRepository->uploadPackageImages($request, $package->id);
            }
            DB::commit();
            session()->flash('success', 'Package created successfully.');
            return response()->json([
                'status' => true, 
                'message' => 'Package created successfully.',
                'redirect_url' => route('package.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to create package: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $package = $this->packageRepository->getById($id);
        return view('crm.package.view', compact('package'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $categories = Category::all();
        $destinations = Destination::active()->orderBy('name')->get();
        $package = $this->packageRepository->getById($id);
        $faqs = $package->faqs;
        $packageAttributes = PackageAttribute::active()
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('type');
        $selectedAttributeIds = $package->packageAttributes->pluck('id')->all();

        return view('crm.package.edit', compact('package', 'faqs', 'categories', 'packageAttributes', 'selectedAttributeIds', 'destinations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PackageRequest  $request, string $id)
    {
        $this->packageRepository->updatePackage($request,$id);
        $this->packageRepository->deleteRemovedImages($request);
        $this->packageRepository->uploadPackageImages($request, $id);
        session()->flash('success', 'Package updated successfully.');
        return response()->json([
            'status' => true, 
            'message' => 'Package updated successfully.',
            'redirect_url' => route('package.index')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->packageRepository->delete($id);
            return response()->json(['status' => true, 'message' => 'Package deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to delete package: ' . $e->getMessage()]);
        }
    }

    protected function initDataTable($request){
        
        $data = $this->packageRepository->initData($request);
        
        return DataTables::of($data)
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && $request->search['value']) {
                    $search = strtolower($request->search['value']);
                    $query->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(package_name) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(package_code) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('CAST(price AS CHAR) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(source_city) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(destination_city) LIKE ?', ["%{$search}%"]);
                    });
                }
            })
            ->addColumn('booking_type', function ($row) {
                if ($row->booking_type == 'Domestic') {
                    return '<span class="badge bg-success">Domestic</span>';
                } elseif ($row->booking_type == 'International') {
                    return '<span class="badge bg-primary">International</span>';
                } else {
                    return '<span class="badge bg-secondary">-</span>';
                }
            })
            ->addColumn('package_name', function ($row) {
                $fullName = $row->package_name ?? '-';
                $shortName = Str::limit($fullName, 50, '...');
                return '
                    <div class="w-150px message-cell" 
                        data-full="'.e($fullName).'" 
                        style="cursor:pointer;">
                        '.$shortName.'
                    </div>
                ';
            })
            ->addColumn('package_code', function ($row) {
                return '<div class="w-150px">' . ($row->package_code ?? '-') . '</div>';
            })
            ->addColumn('source_city', function ($row) {
                return '<div class="w-150px">' . ($row->source_city ?? '-') . '</div>';
            })
            ->addColumn('destination_city', function ($row) {
                return '<div class="w-150px">' . ($row->destination_city ?? '-') . '</div>';
            })
            ->addColumn('price', function ($row) {
                return '<div class="w-150px">' . ($row->price ?? '-') . '</div>';
            })
            ->addColumn('created_at', function ($row) {
                return formateDate($row->created_at);
            })
            ->addColumn('action', function ($row) {
                return view('crm.package.action', compact('row'))->render();
            })
            ->rawColumns([
                'booking_type',
                'package_name',
                'slug',
                'package_code',
                'source_city',
                'destination_city',
                'price',
                'created_at',
                'action'
            ])
            ->make(true);
            
    }

    public function search(Request $request)
    {
        $search = $request->get('search');
        return response()->json([
            'data' => $search
        ]);
    }
}


