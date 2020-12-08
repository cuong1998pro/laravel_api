<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder as IlluminateBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SellerScope implements Scope
{
    public function apply(IlluminateBuilder $builder,  Model $model)
    {
        $builder->has('products');
    }
}