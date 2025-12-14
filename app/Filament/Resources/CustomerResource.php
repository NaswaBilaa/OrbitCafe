<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class CustomerResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Data Pelanggan';
    
    protected static ?string $modelLabel = 'Pelanggan';
    
    protected static ?string $pluralModelLabel = 'Data Pelanggan';
    
    protected static ?string $navigationGroup = 'Manajemen';
    
    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNotNull('nama_lengkap')
            ->whereNotNull('no_telepon')
            ->select('nama_lengkap', 'no_telepon')
            ->selectRaw('COUNT(*) as total_orders')
            ->selectRaw('SUM(total_price) as total_spent')
            ->selectRaw('MAX(created_at) as last_order')
            ->selectRaw('MIN(created_at) as first_order')
            ->groupBy('nama_lengkap', 'no_telepon')
            ->orderBy('last_order', 'desc')
            ->withCasts([
                'last_order' => 'datetime', 
                'first_order' => 'datetime',
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->size('lg')
                    ->icon('heroicon-o-user')
                    ->copyable()
                    ->copyMessage('Nama berhasil disalin')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('no_telepon')
                    ->label('No. Telepon')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-phone')
                    ->copyable()
                    ->copyMessage('Nomor telepon berhasil disalin')
                    ->copyMessageDuration(1500)
                    ->formatStateUsing(fn (string $state): string => 
                        preg_replace('/(\d{4})(\d{4})(\d+)/', '$1-$2-$3', $state)
                    ),

                Tables\Columns\TextColumn::make('total_orders')
                    ->label('Total Pesanan')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('total_spent')
                    ->label('Total Belanja')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold')
                    ->color('success')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('first_order')
                    ->label('Pelanggan Sejak')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('last_order')
                    ->label('Terakhir Order')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->since()
                    ->description(fn ($record) => $record->last_order->format('d M Y, H:i')),
            ])
            ->filters([
                Tables\Filters\Filter::make('total_orders')
                    ->form([
                        Forms\Components\Select::make('orders')
                            ->label('Filter Pesanan')
                            ->options([
                                '1-5' => '1-5 pesanan',
                                '6-10' => '6-10 pesanan',
                                '11-20' => '11-20 pesanan',
                                '20+' => 'Lebih dari 20 pesanan',
                            ])
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['orders'],
                            function (Builder $query, $value): Builder {
                                return match($value) {
                                    '1-5' => $query->havingRaw('COUNT(*) BETWEEN 1 AND 5'),
                                    '6-10' => $query->havingRaw('COUNT(*) BETWEEN 6 AND 10'),
                                    '11-20' => $query->havingRaw('COUNT(*) BETWEEN 11 AND 20'),
                                    '20+' => $query->havingRaw('COUNT(*) > 20'),
                                    default => $query,
                                };
                            }
                        );
                    }),

                Tables\Filters\Filter::make('total_spent')
                    ->form([
                        Forms\Components\Select::make('spent')
                            ->label('Filter Total Belanja')
                            ->options([
                                '0-100000' => 'Rp 0 - 100.000',
                                '100000-500000' => 'Rp 100.000 - 500.000',
                                '500000-1000000' => 'Rp 500.000 - 1.000.000',
                                '1000000+' => 'Lebih dari Rp 1.000.000',
                            ])
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['spent'],
                            function (Builder $query, $value): Builder {
                                return match($value) {
                                    '0-100000' => $query->havingRaw('SUM(total_price) BETWEEN 0 AND 100000'),
                                    '100000-500000' => $query->havingRaw('SUM(total_price) BETWEEN 100000 AND 500000'),
                                    '500000-1000000' => $query->havingRaw('SUM(total_price) BETWEEN 500000 AND 1000000'),
                                    '1000000+' => $query->havingRaw('SUM(total_price) > 1000000'),
                                    default => $query,
                                };
                            }
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('viewOrders')
                    ->label('Lihat Pesanan')
                    ->icon('heroicon-o-shopping-bag')
                    ->color('primary')
                    ->url(fn ($record): string => 
                        route('filament.admin.resources.orders.index', [
                            'tableFilters' => [
                                'nama_lengkap' => ['value' => $record->nama_lengkap],
                            ]
                        ])
                    )
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(fn ($record): string => 
                        'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->no_telepon)
                    )
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('exportSelected')
                        ->label('Export Data')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('primary')
                        ->action(function ($records) {
                            // Implement export logic here
                            // You can use maatwebsite/excel or similar
                        }),
                ]),
            ])
            ->defaultSort('last_order', 'desc')
            ->emptyStateHeading('Belum ada data pelanggan')
            ->emptyStateDescription('Data pelanggan akan muncul setelah ada pesanan.')
            ->emptyStateIcon('heroicon-o-users');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false; // Tidak bisa create manual, data dari orders
    }
    
    public static function getNavigationBadge(): ?string
    {
        return (string) Order::whereNotNull('nama_lengkap')
            ->whereNotNull('no_telepon')
            ->distinct('nama_lengkap', 'no_telepon')
            ->count('nama_lengkap');
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
}