<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Všetci prihlásení používatelia v administrácii s rolou môžu vidieť zoznam
        return $user->role <= User::ROLE_SELLER;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Všetci prihlásení používatelia v administrácii s rolou môžu vytvárať produkty (predvolené)
        return true;
        // Príklad vlastnej logiky: Admin, Editor aj Predajca môžu vytvárať produkty
        // return $user->role <= User::ROLE_SELLER;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        // Predvolené: povoliť všetko
        return true;

        // Príklad vlastnej logiky s kontrolou vlastníctva:
        // Admin a Editor (0, 1, 2) môžu upravovať akýkoľvek produkt
        // if ($user->role <= User::ROLE_EDITOR) {
        //     return true;
        // }
        // Predajca (3) môže upravovať IBA svoj vlastný produkt (Kontrola Vlastníctva)
        // if ($user->role === User::ROLE_SELLER) {
        //     return $product->seller_id === $user->id;
        // }
        // return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        // Predvolené: povoliť všetko
        return true;

        // Príklad vlastnej logiky: Iba Admin a Systém (0, 1) môžu mazať produkty (Editor a Predajca nesmú)
        // return $user->role <= User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Product $product): bool
    {
        return $this->delete($user, $product);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return $this->delete($user, $product);
    }
}
