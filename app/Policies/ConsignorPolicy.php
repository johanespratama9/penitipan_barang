<?php

namespace App\Policies;

use App\Models\Consignor;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConsignorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        // Admin, Kasir, dan Penitip boleh mengakses list
        // (Khusus penitip, query akan di-scope hanya miliknya)
        return $user->hasAnyRole(['admin', 'kasir', 'penitip']);
    }

    public function view(User $user, Consignor $consignor): bool
    {
        // Admin dan Kasir boleh melihat semua
        if ($user->hasAnyRole(['admin', 'kasir'])) {
            return true;
        }

        // Penitip hanya boleh melihat data miliknya sendiri
        if ($user->hasRole('penitip')) {
            return $consignor->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Consignor $consignor): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Consignor $consignor): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, Consignor $consignor): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Consignor $consignor): bool
    {
        return $user->hasRole('admin');
    }
}

