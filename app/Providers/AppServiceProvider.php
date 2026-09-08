<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Super Admin bypass semua Gate/Policy di Filament
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        // Daftarkan RolePolicy yang di-generate Shield (di luar auto-discovery)
        Gate::policy(\Spatie\Permission\Models\Role::class, \App\Policies\RolePolicy::class);

        // Daftarkan CategoryPolicy
        Gate::policy(\App\Models\Category::class, \App\Policies\CategoryPolicy::class);

        // Daftarkan ConsignorPolicy
        Gate::policy(\App\Models\Consignor::class, \App\Policies\ConsignorPolicy::class);

        // Daftarkan ProductPolicy
        Gate::policy(\App\Models\Product::class, \App\Policies\ProductPolicy::class);

        // Daftarkan ConsignmentPolicy
        Gate::policy(\App\Models\Consignment::class, \App\Policies\ConsignmentPolicy::class);

        // Daftarkan SalePolicy
        Gate::policy(\App\Models\Sale::class, \App\Policies\SalePolicy::class);

        // Daftarkan ConsignorPaymentPolicy
        Gate::policy(\App\Models\ConsignorPayment::class, \App\Policies\ConsignorPaymentPolicy::class);
    }
}
