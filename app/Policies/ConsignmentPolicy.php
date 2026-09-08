<?php

namespace App\Policies;

use App\Models\Consignment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConsignmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'kasir', 'penitip']);
    }

    public function view(User $user, Consignment $consignment): bool
    {
        if ($user->hasAnyRole(['admin', 'kasir'])) {
            return true;
        }

        // Penitip hanya boleh melihat data penitipan miliknya
        if ($user->hasRole('penitip')) {
            return $consignment->consignor?->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Consignment $consignment): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Consignment $consignment): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, Consignment $consignment): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Consignment $consignment): bool
    {
        return $user->hasRole('admin');
    }
}
