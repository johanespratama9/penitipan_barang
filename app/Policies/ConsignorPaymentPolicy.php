<?php

namespace App\Policies;

use App\Models\ConsignorPayment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConsignorPaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'penitip']);
    }

    public function view(User $user, ConsignorPayment $payment): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('penitip')) {
            return $payment->consignor?->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, ConsignorPayment $payment): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, ConsignorPayment $payment): bool
    {
        return $user->hasRole('admin');
    }
}

