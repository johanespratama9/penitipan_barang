<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Super Admin bypass via AppServiceProvider Gate::before()
     */

    public function viewAny(User $user): bool
    {
        // Admin dan Kasir dapat melihat daftar kategori
        return $user->hasAnyRole(['admin', 'kasir']);
    }

    public function view(User $user, Category $category): bool
    {
        return $user->hasAnyRole(['admin', 'kasir']);
    }

    public function create(User $user): bool
    {
        // Hanya admin yang dapat membuat kategori
        return $user->hasRole('admin');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, Category $category): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Category $category): bool
    {
        return $user->hasRole('admin');
    }
}
