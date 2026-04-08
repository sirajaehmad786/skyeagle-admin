<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class UserTeamScope implements Scope
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
    
    public function apply(Builder $builder, Model $model)
    {
        // dd($this->user);
            // Super admin bypass
            // if ($this->user->role !== 'super_admin') {
            if ($this->user && !$this->user->hasRole(config('constant.super_admin_role'))) {
                // Get IDs of user and their team
                // $teamUserIds = $this->user->teamMembers()->pluck('id')->toArray();
                $ids = array_merge([$this->user->id], [3]);

                // Apply condition
                $builder->whereIn('parent_id', $ids);
            }
    }
}
