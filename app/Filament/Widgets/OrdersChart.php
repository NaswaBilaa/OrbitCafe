<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Order';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Orderan',
                    'data' => [0, 4, 6, 7, 9, 10, 1, 3, 4, 5, 5, 7]
                ]
                ],
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Des']
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
