<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\PackageAttributeRequest;
use App\Models\PackageAttribute;
use App\Repositories\PackageAttributeRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PackageAttributeController extends Controller
{
    protected PackageAttributeRepository $packageAttributeRepository;

    public function __construct(PackageAttributeRepository $packageAttributeRepository)
    {
        $this->packageAttributeRepository = $packageAttributeRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->initDataTable($request);
        }

        return view('crm.packageAttribute.index');
    }

    public function create()
    {
        $types = PackageAttribute::typeOptions();
        return view('crm.packageAttribute.create', compact('types'));
    }

    public function store(PackageAttributeRequest $request)
    {
        $this->packageAttributeRepository->createAttribute($request);
        session()->flash('success', 'Package attribute created successfully.');

        return response()->json([
            'status' => true,
            'message' => 'Package attribute created successfully.',
            'redirect_url' => route('package-attributes.index'),
        ]);
    }

    public function edit(string $id)
    {
        $attribute = $this->packageAttributeRepository->findById($id);
        $types = PackageAttribute::typeOptions();

        return view('crm.packageAttribute.edit', compact('attribute', 'types'));
    }

    public function update(PackageAttributeRequest $request, string $id)
    {
        $this->packageAttributeRepository->updateAttribute($request, $id);
        session()->flash('success', 'Package attribute updated successfully.');

        return response()->json([
            'status' => true,
            'message' => 'Package attribute updated successfully.',
            'redirect_url' => route('package-attributes.index'),
        ]);
    }

    public function destroy(string $id)
    {
        $this->packageAttributeRepository->delete($id);

        return response()->json([
            'status' => true,
            'message' => 'Package attribute deleted successfully.',
        ]);
    }

    protected function initDataTable($request)
    {
        $data = $this->packageAttributeRepository->initData($request);

        return DataTables::of($data)
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && $request->search['value']) {
                    $search = strtolower($request->search['value']);
                    $query->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(type) LIKE ?', ["%{$search}%"]);
                    });
                }
            })
            ->addColumn('type', function ($row) {
                return PackageAttribute::typeLabel($row->type);
            })
            ->addColumn('name', function ($row) {
                return '<div class="w-150px">' . e($row->name) . '</div>';
            })
            ->addColumn('status', function ($row) {
                return $row->status
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->addColumn('created_at', function ($row) {
                return formateDate($row->created_at);
            })
            ->addColumn('action', function ($row) {
                return view('crm.packageAttribute.action', compact('row'))->render();
            })
            ->rawColumns(['name', 'status', 'action'])
            ->make(true);
    }
}
