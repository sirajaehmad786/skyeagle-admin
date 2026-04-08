<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserHierarchyScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // If no auth (e.g. CLI, seeder) — skip scope
        if (! auth()->check()) {
            return;
        }

        $auth = auth()->user();
        $authLevel = optional($auth->role)->level;

        // If no role defined for auth — skip scope (or you may choose to block)
        if (! $authLevel) {
            return;
        }

        // If Super-Admin (level = 1) — no filtering
        if ($authLevel == 1) {
            return;
        }

        // Otherwise: restrict to children of this user, with lower role level
        $builder->where('parent_id', $auth->id)
                ->whereHas('role', function ($q) use ($authLevel) {
                    $q->where('level', '>', $authLevel);
                });
    }
}
