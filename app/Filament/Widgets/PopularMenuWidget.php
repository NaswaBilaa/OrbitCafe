<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PopularMenuWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    public function getTableRecordKey($record): string
    {
        return (string) $record->id;
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderItem::query()
                    ->select([
                        'order_items.menu_id',
                        'menus.name as menu_name',
                        'menus.price',
                        DB::raw('SUM(order_items.quantity) as total_quantity'),
                        DB::raw('CAST(SUM(order_items.subtotal) AS UNSIGNED) as total_sales')
                    ])
                    ->join('menus', 'order_items.menu_id', '=', 'menus.id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->where('orders.created_at', '>=', Carbon::now()->subDays(30))
                    ->whereIn('orders.status', ['paid', 'processing', 'ready', 'serving', 'completed'])
                    ->groupBy('order_items.menu_id', 'menus.name', 'menus.price')
                    ->orderByDesc('total_quantity')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('row_number')
                    ->label('#')
                    ->rowIndex()
                    ->alignCenter()
                    ->size('sm'),
                    
                Tables\Columns\TextColumn::make('menu_name')
                    ->label('Nama Menu')
                    ->searchable()
                    ->weight('bold')
                    ->wrap(),
                    
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Terjual')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('total_sales')
                    ->label('Total Penjualan')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
            ])
            ->heading('Menu Terlaris (30 Hari Terakhir)')
            ->description('Daftar 10 menu dengan penjualan tertinggi dalam 30 hari terakhir')
            ->defaultPaginationPageOption(10)
            ->poll('30s');
    }
}