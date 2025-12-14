<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Order;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    public function getTableRecordKey(mixed $record): string
    {
        return $record->nama_lengkap . '_' . $record->no_telepon;
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export Data')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                }),
        ];
    }
    
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Pelanggan')
                ->badge(fn () => 
                    Order::whereNotNull('nama_lengkap')
                        ->whereNotNull('no_telepon')
                        ->distinct()
                        ->count('nama_lengkap')
                ),
            
            'new' => Tab::make('Pelanggan Baru')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->havingRaw('COUNT(*) = 1')
                )
                ->badge(fn () => 
                    Order::whereNotNull('nama_lengkap')
                        ->whereNotNull('no_telepon')
                        ->select('nama_lengkap', 'no_telepon')
                        ->selectRaw('COUNT(*) as orders')
                        ->groupBy('nama_lengkap', 'no_telepon')
                        ->having('orders', 1)
                        ->count()
                )
                ->badgeColor('primary'),
            
            'regular' => Tab::make('Pelanggan Tetap')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->havingRaw('COUNT(*) >= 5')
                )
                ->badge(fn () => 
                    Order::whereNotNull('nama_lengkap')
                        ->whereNotNull('no_telepon')
                        ->select('nama_lengkap', 'no_telepon')
                        ->selectRaw('COUNT(*) as orders')
                        ->groupBy('nama_lengkap', 'no_telepon')
                        ->having('orders', '>=', 5)
                        ->count()
                )
                ->badgeColor('success'),
        ];
    }
}