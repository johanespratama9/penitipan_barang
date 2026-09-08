<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesChartWidget extends ChartWidget
{
    protected ?string $heading = 'Grafik Penjualan 7 Hari Terakhir';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'super_admin']) ?? false;
    }

    protected function getData(): array
    {
        $days = collect(range(6, 0))->map(fn ($d) => Carbon::today()->subDays($d));

        $data = $days->map(function ($date) {
            return (float) Sale::whereDate('sold_at', $date)
                ->where('status', 'completed')
                ->sum('total');
        });

        $labels = $days->map(fn ($date) => $date->format('d M'));

        return [
            'datasets' => [
                [
                    'label' => 'Total Penjualan (Rp)',
                    'data' => $data->toArray(),
                    'borderColor' => '#3b82f6',
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => $labels->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
