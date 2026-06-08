<?php

namespace App\Http\Controllers\Crm;

use App\Enums\ActivityAction;
use App\Enums\ActivityType;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return self::initDataTable($request);
        }
        return view('crm.user.index');
    }

    public function create()
    {
        $parentUsers = $this->userRepository->userList();
        return view('crm.user.create', compact('parentUsers'));
    }

    public function store(UserCreateRequest $request)
    {
        try {
            $user = User::create($request->all());
            activityLog(
                'User Module',
                ActivityType::USER,
                ActivityAction::CREATE,
                User::class,
                $user->id,
                'User created',
                [],
                $user->toArray(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_by' => auth()->id()
                ]
            );
            session()->flash('success', 'User created successfully');
            return response()->json([
                'status' => true,
                'redirect' => route('users.index')
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => "Something went wrong"
            ]);
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $parentUsers = $this->userRepository->userList();
            return view('crm.user.edit', compact('user', 'parentUsers'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('users.index')->with('error', "Something went wrong");
        }
    }

    public function update(UserUpdateRequest $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $oldValues = $user->toArray();
            $this->userRepository->update($request, $id);
            $user = User::findOrFail($id);
            $newValues = $user->toArray();
            activityLog(
                'User Module',
                ActivityType::USER,
                ActivityAction::UPDATE,
                User::class,
                $user->id,
                'User updated',
                $oldValues,
                $newValues,
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'updated_by' => auth()->id()
                ]
            );
            session()->flash('success', 'User updated successfully');
            return response()->json([
                'status' => true,
                'redirect_url' => route('users.index')
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => "Something went wrong"
            ]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $id = (int) $id;
            $user = User::findOrFail($id);
            $oldValues = $user->toArray();
            $user->delete();
            activityLog(
                'User Module',
                ActivityType::USER,
                ActivityAction::DELETE,
                User::class,
                $id,
                'User deleted',
                $oldValues,
                [],
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'deleted_by' => auth()->id()
                ]
            );
            return response()->json(['status' => true, 'message' => 'User deleted successfully.']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => "Something went wrong"
            ]);
        }
    }

    protected function initDataTable($request)
    {
        $data = $this->userRepository->initData($request);
        return DataTables::of($data)
            ->orderColumn('created_at', 'created_at $1')
            ->filter(function ($query) use ($request) {
                if ($request->filled('name_search')) {
                    $search = $request->name_search;
                    $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
                }
            })
            ->addColumn('profile_image', function ($data) {
                $imageUrl = $data->profile_image
                    ? asset('storage/profileImage/' . $data->profile_image)
                    : asset('images/users/istockphoto-1337144146-612x612.jpg');

                return '<img 
                            src="' . $imageUrl . '" 
                            alt="profile" 
                            style="width:40px; height:40px; object-fit:cover; border-radius:50%; display:block;"
                        >';
            })
            ->addColumn('name', function ($data) {
                return '<div class="w-100px">' . $data->name . '</div>';
            })
            ->addColumn('email', function ($data) {
                return '<div class="w-100px">' . $data->email . '</div>';
            })
            ->addColumn('phone', function ($data) {
                return '<div class="w-100px">' . $data->phone . '</div>';
            })
            ->addColumn('parent', function ($data) {
                return '<div class="w-100px">' . ($data->parent?->name ?? '-') . '</div>';
            })
            ->addColumn('status', function ($data) {
                $sts = ($data->status == config("constant.user_status.Active")) ? 'badge-outline-success' : 'badge-outline-danger';
                return '<span class="badge fs-6 ' . $sts . '">' . $data->status . '</span>';
            })
            ->addColumn('created_at', function ($row) {
                return formateDate($row->created_at);
            })
            ->addColumn('action', function ($row) {
                return view('crm.user.action', compact('row'))->render();
            })
            ->rawColumns(['profile_image', 'name', 'email', 'phone', 'parent', 'status', 'created_at', 'action'])
            ->make(true);
    }
}
