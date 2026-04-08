<?php 
namespace App\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class UserQuotationScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role && $user->role->name === config('constant.super_admin_role')) {
                return;
            }

            $userIds = User::hierarchyUserIdsFor($user);

            $builder->whereIn($model->getTable().'.user_id', $userIds);
        }
    }
}
