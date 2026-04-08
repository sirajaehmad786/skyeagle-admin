<?php

namespace App\Http\Controllers\Crm;

use App\Enums\ActivityAction;
use App\Enums\ActivityType;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{

    public function __construct() {
        $this->middleware('permission:role-list')->only('index');
        $this->middleware('permission:role-add')->only('create', 'store');
        $this->middleware('permission:role-edit')->only('edit', 'update');
        $this->middleware('permission:role-delete')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->initDataTable();
        }
        return view('crm.role.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all();
        // Group permissions by prefix before the dot or dash
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            // If you use dash naming
            if (strpos($permission->name, '-') !== false) {
                return explode('-', $permission->name)[0] ?? $permission->name;
            }
            return explode('.', $permission->name)[0];
        });

        return view('crm.role.create', compact('groupedPermissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        try {
            $role = Role::create([
                "name" => $request->name,
                "level" => $request->level,
            ]);
            $role->syncPermissions($request->permissions);
            activityLog(
                'Role Module',
                ActivityType::ROLE,
                ActivityAction::CREATE,
                Role::class,
                $role->id,
                'Role created',
                [],
                $role->toArray(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_by' => auth()->id()
                ]
            );
            session()->flash('success', 'Role created successfully');
            return response()->json([
                'status' => true,
                'redirect_url' => route('roles.index')
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => "Something went wrong"
            ], 500);
        }
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
        try{
            $role = Role::findOrFail($id);
            $permissions = Permission::all();
            // Group permissions by module
            $groupedPermissions = $permissions->groupBy(function ($permission) {
                // If you use dash naming
                if (strpos($permission->name, '-') !== false) {
                    return explode('-', $permission->name)[0] ?? $permission->name;
                }
                return explode('.', $permission->name)[0];
            });
            $rolePermissions = $role->permissions->pluck('name')->toArray();
            return view('crm.role.edit', compact('role', 'groupedPermissions', 'rolePermissions'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('roles.index')->with('error', "Something went wrong");;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{     
            // Validation
            $validator = Validator::make($request->all(), [
                'name' => [
                    'required',
                    Rule::unique('roles', 'name')->ignore($id)
                ],
                'permissions' => 'required|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update
            $role = Role::findOrFail($id);
            $oldValues = $role->toArray();
            $role->update([
                'name' => $request->name,
                'level' => $request->level
            ]);
            $role->syncPermissions($request->permissions);
            activityLog(
                'Role Module',
                ActivityType::ROLE,
                ActivityAction::UPDATE,
                Role::class,
                $role->id,
                'Role updated',
                $oldValues,
                $role->toArray(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'updated_by' => auth()->id()
                ]
            );
            session()->flash('success', 'Role updated successfully');
            return response()->json([
                'status' => true,
                'redirect_url' => route('roles.edit', $id)
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => "Something went wrong"
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $id = (int) $id;
            $role = Role::findOrFail($id);
            $oldValues = $role->toArray();
            $role->delete();
            activityLog(
                'Role Module',
                ActivityType::ROLE,
                ActivityAction::DELETE,
                Role::class,
                $id,
                'Role deleted',
                $oldValues,
                [],
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'deleted_by' => auth()->id()
                ]
            );
            return response()->json(['message' => 'Role deleted successfully.']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('roles.index')->with('error', "Something went wrong");;
        }
    }

    /**
     * initDataTable function use for load data
     */
    protected function initDataTable()
    {
        $data = Role::query();
        return DataTables::of($data)
            ->addColumn('name', function($data){
                return '<div class="w-100px">' . $data->name .'</div>';
            })
            ->addColumn('level', function($data){
                return '<div class="w-100px">' . $data->level .'</div>';
            })
            ->addColumn('action', function ($row) {
                return view('crm.role.action', compact('row'))->render();
            })
            ->rawColumns(['name','level','action'])
            ->make(true);
    }
}
