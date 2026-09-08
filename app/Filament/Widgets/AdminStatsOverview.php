<?php

namespace App\Filament\Widgets;

use App\Models\Consignor;
use App\Models\ConsignorBalance;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'super_admin']) ?? false;
    }

    protected function getStats(): array
    {
        $todaySales = Sale::whereDate('sold_at', today())->where('status', 'completed')->sum('total');
        $monthSales = Sale::whereMonth('sold_at', now()->month)->whereYear('sold_at', now()->year)->where('status', 'completed')->sum('total');
        $totalCommission = SaleItem::sum('commission_amount');
        $totalConsignorDebt = ConsignorBalance::sum('balance');

        $totalProducts = Product::count();
        $availableProducts = Product::where('status', 'available')->count();
        $soldProducts = Product::where('status', 'sold')->count();
        $totalConsignors = Consignor::count();

        return [
            Stat::make('Penjualan Hari Ini', 'Rp ' . number_format((float) $todaySales, 0, ',', '.'))
                ->description('Bulan ini: Rp ' . number_format((float) $monthSales, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Omzet Toko', 'Rp ' . number_format((float) Sale::where('status', 'completed')->sum('total'), 0, ',', '.'))
                ->description('Komisi Toko: Rp ' . number_format((float) $totalCommission, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Kewajiban ke Penitip', 'Rp ' . number_format((float) $totalConsignorDebt, 0, ',', '.'))
                ->description('Saldo penitip belum dicairkan')
                ->descriptionIcon('heroicon-m-scale')
                ->color('warning'),

            Stat::make('Katalog Produk', "{$availableProducts} / {$totalProducts}")
                ->description("Tersedia {$availableProducts} | Terjual {$soldProducts}")
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),

            Stat::make('Total Penitip', (string) $totalConsignors)
                ->description('Mitra penitip aktif terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('gray'),

            Stat::make('Total Transaksi', (string) Sale::where('status', 'completed')->count())
                ->description('Invoice selesai diproses')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('success'),
        ];
    }
}

