<?php

namespace App\Repositories;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Illuminate\Support\Facades\File; // Add this
use Illuminate\Support\Facades\Storage;

class UserRepository extends BaseRepository
{

    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function update($request, $id)
    {
        
        $user = $this->findOrFail($id);
        $data = $request->all();
        if (filled($request->password)) {
            $user->password = $request->password;
        }
        
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->al_phone = $request->al_phone;
        $user->status = $request->status;
        $user->parent_id = $request->parent_id;
        $user->save($data);
        $user->syncRoles($request->role);
        $user->role_id = $user->roles->first()->id;
        $user->save();
        return $user;
    }

    public function countries()
    {
        return Country::select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function states($countryId = null)
    {
        $query = State::select('id', 'name', 'country_id')
            ->orderBy('name');

        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        return $query->get();
    }

    public function cities($stateId = null, $countryId = null)
    {
        $query = City::select('id', 'name', 'state_id', 'country_id')
            ->orderBy('name');

        if ($stateId) {
            $query->where('state_id', $stateId);
        }

        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        return $query->get();
    }

    // List of parent users
    public function userList()
    {
        $authUser  = auth()->user();
        $authLevel = $authUser->role->level;

        // SUPER ADMIN (highest level)
        if ($authLevel == 1) {
            $users = User::select('id', 'first_name', 'last_name', 'role_id')->with('role')->get();
        }
        else {
            
            // ONLY his own created users AND only lower-level roles
            $users = User::select('id', 'first_name', 'last_name', 'role_id')
                ->with('role')
                ->where(function ($q) use ($authUser) {
                    $q->where('parent_id', $authUser->id)
                      ->orWhere('id', $authUser->id);
                })
                ->whereHas('role', function ($q) use ($authLevel) {
                    $q->where('level', '>=', $authLevel);
                })->get();
        }
        return $users;
        
    }

    public function initData($request)
    {
        $authUser  = auth()->user();
        $authLevel = $authUser->role->level;

        // SUPER ADMIN (highest level)
        if ($authLevel == 1) {
            $users = User::with('role');
        }
        else {
            // ONLY his own created users AND only lower-level roles
            $users = User::with('role')
                ->where('parent_id', $authUser->id)
                ->whereHas('role', function ($q) use ($authLevel) {
                    $q->where('level', '>', $authLevel);
                });
        }
        return $users;
    }
}
