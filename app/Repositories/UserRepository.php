<?php

namespace App\Repositories;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;

class UserRepository extends BaseRepository
{

    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function update($request, $id)
    {
        $user = $this->findOrFail($id);

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

    public function userList()
    {
        return User::select('id', 'first_name', 'last_name')->get();
    }

    public function initData($request)
    {
        $query = User::query()->with('parent');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        if ($request->filled('created_from')) {
            $query->where('created_at', '>=', istDateRangeToUtc($request->created_from));
        }

        if ($request->filled('created_to')) {
            $query->where('created_at', '<=', istDateRangeToUtc($request->created_to, true));
        }

        return $query;
    }
}
