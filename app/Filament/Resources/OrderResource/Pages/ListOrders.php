<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Order;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

     public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Pesanan')
                ->badge(fn () => Order::count()),
            
            'active' => Tab::make('Pesanan Baru')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereIn('status', ['paid', 'processing', 'ready', 'serving'])
                )
                ->badge(fn () => Order::whereIn('status', ['paid', 'processing', 'ready', 'serving'])->count())
                ->badgeColor('primary'),

            'completed' => Tab::make('Pesanan Selesai')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed'))
                ->badge(fn () => Order::where('status', 'completed')->count())
                ->badgeColor('success'),
                
            'failed_cancelled' => Tab::make('Gagal')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereIn('status', ['cancelled', 'failed'])
                )
                ->badge(fn () => Order::whereIn('status', ['cancelled', 'failed'])->count())
                ->badgeColor('error'),
        ];
    }

}
