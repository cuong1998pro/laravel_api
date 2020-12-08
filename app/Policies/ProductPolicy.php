<?php

namespace App\Policies;

use App\Product;
use App\Traits\AdminActions;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization, AdminActions;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Product  $product
     * @return mixed
     */
    public function addCategory(User $user, Product $product)
    {
        return $user->id == $product->seller->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function deleteCategory(User $user, Product $product)
    {
        return $user->id == $product->seller->id;
    }
}
