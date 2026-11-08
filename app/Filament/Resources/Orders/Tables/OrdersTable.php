<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label(__('Number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),
                TextColumn::make('user.name')
                    ->label(__('Customer'))
                    ->searchable()
                    ->sortable(),
                // Popisok a farbu badge berie Filament z enumu (HasLabel, HasColor)
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),
                TextColumn::make('payment_status')
                    ->label(__('Payment'))
                    ->badge(),
                TextColumn::make('total')
                    ->label(__('Total'))
                    ->money('EUR')
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('items_count')
                    ->label(__('Items'))
                    ->counts('items'),
                TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(OrderStatus::class),
                SelectFilter::make('payment_status')
                    ->label(__('Payment Status'))
                    ->options(PaymentStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
