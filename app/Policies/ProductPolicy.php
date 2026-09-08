<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'kasir', 'penitip']);
    }

    public function view(User $user, Product $product): bool
    {
        if ($user->hasAnyRole(['admin', 'kasir'])) {
            return true;
        }

        // Penitip hanya boleh melihat produk miliknya sendiri
        if ($user->hasRole('penitip')) {
            return $product->consignor?->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, Product $product): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return $user->hasRole('admin');
    }
}

