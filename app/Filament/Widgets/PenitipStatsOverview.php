<?php

namespace App\Filament\Widgets;

use App\Models\Consignor;
use App\Models\Product;
use App\Models\SaleItem;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PenitipStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('penitip') ?? false;
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $consignor = Consignor::where('user_id', $user?->id)->with('balance')->first();

        if (!$consignor) {
            return [
                Stat::make('Profil Penitip', 'Belum Terhubung')
                    ->description('Hubungi admin toko untuk menghubungkan data profil')
                    ->color('warning'),
            ];
        }

        $totalProducts = Product::where('consignor_id', $consignor->id)->count();
        $availableProducts = Product::where('consignor_id', $consignor->id)->where('status', 'available')->count();
        $soldProducts = Product::where('consignor_id', $consignor->id)->where('status', 'sold')->count();

        $balance = $consignor->balance;
        $totalEarned = (float) ($balance?->total_earned ?? 0);
        $totalPaid = (float) ($balance?->total_paid ?? 0);
        $currentBalance = (float) ($balance?->balance ?? 0);

        return [
            Stat::make('Saldo Belum Dicairkan', 'Rp ' . number_format($currentBalance, 0, ',', '.'))
                ->description('Hak penjualan Anda yang belum dibayar toko')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('warning'),

            Stat::make('Total Sudah Diterima', 'Rp ' . number_format($totalPaid, 0, ',', '.'))
                ->description('Total dana yang sudah ditransfer/dibayar')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Total Pendapatan Bersih', 'Rp ' . number_format($totalEarned, 0, ',', '.'))
                ->description('Total hak penitip dari seluruh barang laku')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Barang Saya', "{$totalProducts} Total")
                ->description("Tersedia: {$availableProducts} | Terjual: {$soldProducts}")
                ->descriptionIcon('heroicon-m-tag')
                ->color('info'),
        ];
    }
}

