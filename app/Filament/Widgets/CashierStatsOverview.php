<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use App\Models\SaleItem;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CashierStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('kasir') ?? false;
    }

    protected function getStats(): array
    {
        $cashierId = auth()->id();

        $todaySalesCount = Sale::where('cashier_id', $cashierId)
            ->whereDate('sold_at', today())
            ->where('status', 'completed')
            ->count();

        $todayTotalOmzet = Sale::where('cashier_id', $cashierId)
            ->whereDate('sold_at', today())
            ->where('status', 'completed')
            ->sum('total');

        $todayItemsSold = SaleItem::whereHas('sale', function ($q) use ($cashierId) {
            $q->where('cashier_id', $cashierId)->whereDate('sold_at', today())->where('status', 'completed');
        })->sum('quantity');

        return [
            Stat::make('Omzet Kasir Hari Ini', 'Rp ' . number_format((float) $todayTotalOmzet, 0, ',', '.'))
                ->description('Total pendapatan kasir Anda')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Jumlah Transaksi Hari Ini', (string) $todaySalesCount)
                ->description('Invoice berhasil diproses')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('primary'),

            Stat::make('Produk Terjual Hari Ini', (string) $todayItemsSold . ' item')
                ->description('Unit barang terjual via kasir')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info'),
        ];
    }
}

