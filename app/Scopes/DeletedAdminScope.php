<?php

namespace App\Scopes;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class DeletedAdminScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if(Auth::check() && Auth::user()->is_admin){
            $builder->withTrashed();
            // $builder->withoutGlobalScope('Illuminate\Database\Eloquent\SoftDeletingScope');
        }
    }
}