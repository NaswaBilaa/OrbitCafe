<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Carbon;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Data Pesanan';
    
    protected static ?string $modelLabel = 'Pesanan';
    
    protected static ?string $pluralModelLabel = 'Data Pesanan';

    protected static ?string $navigationGroup = 'Manajemen';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pesanan')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Nomor Invoice')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'processing' => 'Processing',
                                'ready' => 'Ready',
                                'serving' => 'Serving',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'failed' => 'Failed',
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Pelanggan')
                    ->schema([
                        Forms\Components\TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('no_telepon')
                            ->label('No. Telepon')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\Select::make('table_id')
                            ->label('Meja')
                            ->relationship('table', 'table_number')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Detail Pesanan')
                    ->schema([
                        Forms\Components\Placeholder::make('items')
                            ->label('Item Pesanan')
                            ->content(function (Order $record): string {
                                return view('filament.order-items', ['order' => $record])->render();
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Invoice disalin!')
                    ->weight('bold'),
                    
                TextColumn::make('nama_lengkap')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('no_telepon')
                    ->label('No. Telepon')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('table.no_meja')
                    ->label('Meja')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                    
                TextColumn::make('items_count')
                    ->label('Jumlah Item')
                    ->counts('items')
                    ->sortable()
                    ->alignCenter(),
                    
                TextColumn::make('total_price')
                    ->label('Total Harga')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                    
                TextColumn::make('payment.payment_method')
                    ->label('Metode Pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'qris' => 'info',
                        'transfer' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->toggleable(),
                    
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'pending',
                        'info' => 'paid',
                        'warning' => 'processing',
                        'primary' => 'ready',
                        'success' => fn (string $state): bool => in_array($state, ['serving', 'completed']),
                        'danger' => fn (string $state): bool => in_array($state, ['cancelled', 'failed', 'expired']),
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                    
                TextColumn::make('created_at')
                    ->label('Tanggal Order')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'processing' => 'Processing',
                        'ready' => 'Ready',
                        'serving' => 'Serving',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'failed' => 'Failed',
                    ])
                    ->multiple(),
                    
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                    
                SelectFilter::make('table_id')
                    ->label('Meja')
                    ->relationship('table', 'table_number')
                    ->multiple(),
            ])
            ->actions([
                Action::make('see_detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn (Order $record) => 'Detail Order #' . $record->invoice_number)
                    ->modalButton('Tutup')
                    ->color('gray')
                    ->modalContent(function (Order $record) {
                        return view('filament.detail', ['order' => $record]);
                    })
                    ->modalWidth('4xl'),

                Action::make('start_processing')
                    ->label('Proses')
                    ->color('warning')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->modalHeading('Mulai Memproses Order?')
                    ->modalDescription('Order akan diubah statusnya menjadi processing.')
                    ->action(function (Order $record) {
                        $record->update(['status' => 'processing']);
                        \Filament\Notifications\Notification::make()
                            ->title('Order Mulai Diproses')
                            ->body('Order #' . $record->invoice_number . ' sedang diproses.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Order $record) => $record->status === 'paid'),

                Action::make('mark_ready')
                    ->label('Siap')
                    ->color('primary')
                    ->icon('heroicon-o-check-badge')
                    ->requiresConfirmation()
                    ->modalHeading('Tandai Order Siap?')
                    ->modalDescription('Order akan siap untuk disajikan/diambil.')
                    ->action(function (Order $record) {
                        $record->update(['status' => 'ready']);
                        \Filament\Notifications\Notification::make()
                            ->title('Order Siap')
                            ->body('Order #' . $record->invoice_number . ' siap disajikan.')
                            ->info()
                            ->send();
                    })
                    ->visible(fn (Order $record) => $record->status === 'processing'),

                Action::make('start_serving')
                    ->label('Antar')
                    ->color('info')
                    ->icon('heroicon-o-truck')
                    ->requiresConfirmation()
                    ->modalHeading('Antar Order ke Meja?')
                    ->modalDescription('Order akan diantar ke meja pelanggan.')
                    ->action(function (Order $record) {
                        $record->update(['status' => 'serving']);
                        \Filament\Notifications\Notification::make()
                            ->title('Order Sedang Diantar')
                            ->body('Order #' . $record->invoice_number . ' sedang diantar.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Order $record) => $record->status === 'ready'),

                Action::make('mark_completed')
                    ->label('Selesai')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Selesaikan Order?')
                    ->modalDescription('Order akan ditandai sebagai selesai.')
                    ->action(function (Order $record) {
                        $record->update(['status' => 'completed']);
                        \Filament\Notifications\Notification::make()
                            ->title('Order Selesai')
                            ->body('Order #' . $record->invoice_number . ' telah selesai.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Order $record) => $record->status === 'serving'),

                Action::make('reject')
                    ->label('Batalkan')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Order?')
                    ->modalDescription('Order akan dibatalkan dan tidak dapat diubah kembali.')
                    ->action(function (Order $record) {
                        $record->update(['status' => 'cancelled']);
                        \Filament\Notifications\Notification::make()
                            ->title('Order Dibatalkan')
                            ->body('Order #' . $record->invoice_number . ' telah dibatalkan.')
                            ->danger()
                            ->send();
                    })
                    ->visible(fn (Order $record) => in_array($record->status, ['paid', 'processing'])),
                    
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_completed')
                        ->label('Tandai Selesai')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each->update(['status' => 'completed']);
                            \Filament\Notifications\Notification::make()
                                ->title('Order Ditandai Selesai')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $pendingCount = Order::where('status', 'pending')->count();
        $paidCount = Order::where('status', 'paid')->count();
        
        $total = $pendingCount + $paidCount;
        
        return $total > 0 ? (string) $total : null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return Order::where('status', 'paid')->count() > 0 ? 'success' : 'warning';
    }
}