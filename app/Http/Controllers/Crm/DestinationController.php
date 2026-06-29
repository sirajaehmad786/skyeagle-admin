<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\DestinationRequest;
use App\Repositories\DestinationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DestinationController extends Controller
{
    public function __construct(protected DestinationRepository $destinationRepository)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->initDataTable($request);
        }

        return view('crm.destination.index');
    }

    public function create()
    {
        return view('crm.destination.create');
    }

    public function store(DestinationRequest $request)
    {
        $this->destinationRepository->createDestination($request);
        session()->flash('success', 'Destination created successfully.');

        return response()->json([
            'status' => true,
            'message' => 'Destination created successfully.',
            'redirect_url' => route('destinations.index'),
        ]);
    }

    public function edit(string $id)
    {
        $destination = $this->destinationRepository->find($id);

        return view('crm.destination.edit', compact('destination'));
    }

    public function update(DestinationRequest $request, string $id)
    {
        $this->destinationRepository->updateDestination($request, (int) $id);
        session()->flash('success', 'Destination updated successfully.');

        return response()->json([
            'status' => true,
            'message' => 'Destination updated successfully.',
            'redirect_url' => route('destinations.index'),
        ]);
    }

    public function destroy(string $id)
    {
        $this->destinationRepository->deleteDestination((int) $id);

        return response()->json([
            'status' => true,
            'message' => 'Destination deleted successfully.',
        ]);
    }

    protected function initDataTable(Request $request)
    {
        $data = $this->destinationRepository->initData();

        return DataTables::of($data)
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && $request->search['value']) {
                    $search = strtolower($request->search['value']);
                    $query->where(function ($query) use ($search) {
                        $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(country) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(city) LIKE ?', ["%{$search}%"]);
                    });
                }
            })
            ->addColumn('name', fn ($row) => '<div class="w-150px">' . e(Str::limit($row->name, 50)) . '</div>')
            ->addColumn('location', fn ($row) => '<div class="w-150px">' . e(collect([$row->city, $row->country])->filter()->implode(', ') ?: '-') . '</div>')
            ->addColumn('packages_count', fn ($row) => '<div class="w-100px">' . (int) $row->packages_count . '</div>')
            ->addColumn('status', fn ($row) => $row->status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>')
            ->addColumn('created_at', fn ($row) => formateDate($row->created_at))
            ->addColumn('action', fn ($row) => view('crm.destination.action', compact('row'))->render())
            ->rawColumns(['name', 'location', 'packages_count', 'status', 'action'])
            ->make(true);
    }
}
