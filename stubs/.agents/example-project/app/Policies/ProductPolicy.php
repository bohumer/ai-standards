<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array((int) $user->role, [0, 1, 2], true);
    }

    public function view(User $user, Product $product): bool
    {
        return in_array((int) $user->role, [0, 1, 2], true) || $product->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return in_array((int) $user->role, [0, 1, 2], true);
    }

    public function update(User $user, Product $product): bool
    {
        return in_array((int) $user->role, [0, 1, 2], true) || $product->user_id === $user->id;
    }

    public function delete(User $user, Product $product): bool
    {
        return in_array((int) $user->role, [0, 1], true) || $product->user_id === $user->id;
    }
}
