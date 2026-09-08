<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'kasir']);
    }

    public function view(User $user, Sale $sale): bool
    {
        return $user->hasAnyRole(['admin', 'kasir']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'kasir']);
    }

    public function update(User $user, Sale $sale): bool
    {
        // Kasir tidak boleh mengubah transaksi yang sudah selesai
        return $user->hasRole('admin');
    }

    public function delete(User $user, Sale $sale): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }
}

