<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class BranchScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $activeBranches = $user->branches()
                ->where('is_active', 1)
                ->pluck('branches.id');

            $builder->whereIn($model->getTable() . '.branch_id', $activeBranches);
        }
    }
}
