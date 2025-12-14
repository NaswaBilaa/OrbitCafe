<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        
        // Total Menu
        $totalMenu = Menu::count();
        
        // Total Pelanggan Hari Ini (berdasarkan unique orders)
        $customersToday = Order::whereDate('created_at', $today)->count();
        
        // Total Order Hari Ini
        $ordersToday = Order::whereDate('created_at', $today)->count();
        
        // Total Penjualan Hari Ini
        $salesToday = Order::whereDate('created_at', $today)
            ->whereIn('status', ['paid', 'processing', 'ready', 'serving', 'completed'])
            ->sum('total_price');
        
        // Data kemarin untuk perbandingan
        $yesterday = Carbon::yesterday();
        $ordersYesterday = Order::whereDate('created_at', $yesterday)->count();
        $salesYesterday = Order::whereDate('created_at', $yesterday)
            ->whereIn('status', ['paid', 'processing', 'ready', 'serving', 'completed'])
            ->sum('total_price');
        
        // Hitung persentase perubahan
        $orderChange = $ordersYesterday > 0 
            ? (($ordersToday - $ordersYesterday) / $ordersYesterday) * 100 
            : 0;
            
        $salesChange = $salesYesterday > 0 
            ? (($salesToday - $salesYesterday) / $salesYesterday) * 100 
            : 0;

        return [
            Stat::make('Total Menu', $totalMenu)
                ->description('Total menu tersedia')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
                
            Stat::make('Pelanggan Hari Ini', $customersToday)
                ->description('Jumlah pesanan hari ini')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart([3, 5, 7, 4, 6, 8, 7, 9]),
                
            Stat::make('Order Hari Ini', $ordersToday)
                ->description($orderChange >= 0 
                    ? number_format(abs($orderChange), 1) . '% naik dari kemarin' 
                    : number_format(abs($orderChange), 1) . '% turun dari kemarin')
                ->descriptionIcon($orderChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($orderChange >= 0 ? 'success' : 'danger')
                ->chart([2, 4, 3, 7, 5, 8, 6, $ordersToday]),
                
            Stat::make('Penjualan Hari Ini', 'Rp ' . number_format($salesToday, 0, ',', '.'))
                ->description($salesChange >= 0 
                    ? number_format(abs($salesChange), 1) . '% naik dari kemarin' 
                    : number_format(abs($salesChange), 1) . '% turun dari kemarin')
                ->descriptionIcon($salesChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($salesChange >= 0 ? 'success' : 'danger')
                ->chart([50000, 70000, 60000, 90000, 80000, 100000, 85000, $salesToday / 1000]),
        ];
    }
    
    protected static ?int $sort = 1;
}