<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;
use Illuminate\Support\Carbon;

class SalesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Laporan Penjualan (7 Hari Terakhir)';
    
    protected static ?int $sort = 2;
    
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $dates = collect(range(6, 0))->map(function ($days) {
            return Carbon::today()->subDays($days);
        });

        $salesData = $dates->map(function ($date) {
            return Order::whereDate('created_at', $date)
                ->whereIn('status', ['paid', 'processing', 'ready', 'serving', 'completed'])
                ->sum('total_price');
        });

        $orderCounts = $dates->map(function ($date) {
            return Order::whereDate('created_at', $date)->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Total Penjualan (Rp)',
                    'data' => $salesData->toArray(),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Jumlah Order',
                    'data' => $orderCounts->toArray(),
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $dates->map(fn ($date) => $date->format('d M'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Total Penjualan (Rp)',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Jumlah Order',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
        ];
    }
}