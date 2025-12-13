<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return 'Cafe Management';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Order ID')->sortable(),
                TextColumn::make('user.name')->label('Customer')->searchable(),
                TextColumn::make('total_price')->label('Total Price')->searchable(),
                TextColumn::make('payment.payment_method')->label('Payment Method')->searchable(),
                TextColumn::make('created_at')->label('Order Date')->dateTime('d M Y H:i')->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'pending',
                        'success' => 'accepted',
                        'danger' => 'rejected',
                    ])
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('see_detail')
                    ->label('See Detail')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Order Details')
                    ->modalButton('Close')
                    ->color('gray')
                    ->modalContent(function (Order $record) {
                        return view('filament.detail', ['order' => $record]);
                    })
                    ->visible(fn (Order $record) => $record->status === 'paid'),

                Action::make('accept')
                    ->label('Accept Order')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('payment_method')
                            ->label('What payment?')
                            ->required(),
                    ])
                    ->action(function (Order $record, array $data) {
                        // Simpan payment_method ke tabel Payment
                        $record->payment()->updateOrCreate(
                            ['order_id' => $record->id],
                            ['payment_method' => $data['payment_method']]
                        );

                        // Update status order jadi paid
                        $record->update(['status' => 'paid']);
                    })
                    ->visible(fn (Order $record) => $record->status === 'pending'),

                Action::make('reject')
                    ->label('Reject Order')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->action(function (Order $record) {
                        $record->update(['status' => 'cancelled']);
                    })
                    ->visible(fn (Order $record) => $record->status === 'pending'),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
}
