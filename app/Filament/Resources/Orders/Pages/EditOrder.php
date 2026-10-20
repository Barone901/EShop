<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('confirm')
                ->label('Confirm')
                ->icon('heroicon-o-check')
                ->color('info')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->status === 'pending')
                ->action(fn () => $this->record->update(['status' => 'confirmed'])),

            Actions\Action::make('process')
                ->label('Process')
                ->icon('heroicon-o-cog')
                ->color('primary')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->status === 'confirmed')
                ->action(fn () => $this->record->update(['status' => 'processing'])),

            Actions\Action::make('ship')
                ->label('Ship')
                ->icon('heroicon-o-truck')
                ->color('purple')
                ->requiresConfirmation()
                ->visible(fn (): bool => in_array($this->record->status, ['confirmed', 'processing']))
                ->action(fn () => $this->record->update(['status' => 'shipped'])),

            Actions\Action::make('deliver')
                ->label('Mark Delivered')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->status === 'shipped')
                ->action(fn () => $this->record->update([
                    'status' => 'delivered',
                    'payment_status' => $this->record->payment_method === 'cod' ? 'paid' : $this->record->payment_status,
                ])),

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Cancel Order')
                ->modalDescription('Are you sure you want to cancel this order? This will return products to stock.')
                ->visible(fn (): bool => !in_array($this->record->status, ['shipped', 'delivered', 'cancelled']))
                ->action(function () {
                    // Return products to stock
                    foreach ($this->record->items as $item) {
                        if ($item->product) {
                            $item->product->increment('stock', $item->quantity);
                        }
                    }
                    $this->record->update([
                        'status' => 'cancelled',
                        'payment_status' => 'failed',
                    ]);
                }),

            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

