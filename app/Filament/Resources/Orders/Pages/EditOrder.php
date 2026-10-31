<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Exceptions\OrderException;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Filament\Actions;
use Filament\Notifications\Notification;
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
                ->visible(fn (): bool => $this->record->status === OrderStatus::Pending)
                ->action(fn () => $this->record->update(['status' => OrderStatus::Confirmed])),

            Actions\Action::make('process')
                ->label('Process')
                ->icon('heroicon-o-cog')
                ->color('primary')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->status === OrderStatus::Confirmed)
                ->action(fn () => $this->record->update(['status' => OrderStatus::Processing])),

            Actions\Action::make('ship')
                ->label('Ship')
                ->icon('heroicon-o-truck')
                ->color('purple')
                ->requiresConfirmation()
                ->visible(fn (): bool => in_array($this->record->status, [OrderStatus::Confirmed, OrderStatus::Processing], true))
                ->action(fn () => $this->record->update(['status' => OrderStatus::Shipped])),

            Actions\Action::make('deliver')
                ->label('Mark Delivered')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->status === OrderStatus::Shipped)
                ->action(fn () => $this->record->update([
                    'status' => OrderStatus::Delivered,
                    // Dobierka je pri doruceni zaplatena
                    'payment_status' => $this->record->payment_method === PaymentMethod::CashOnDelivery
                        ? PaymentStatus::Paid
                        : $this->record->payment_status,
                ])),

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Cancel Order')
                ->modalDescription('Are you sure you want to cancel this order? This will return products to stock.')
                ->visible(fn (): bool => $this->record->status->isCancellableByAdmin())
                ->action(function () {
                    // Rovnake rusenie ako u zakaznika (transakcia, zamok, vratenie na sklad)
                    try {
                        app(OrderService::class)->cancelOrder($this->getOrder(), byAdmin: true);
                    } catch (OrderException $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }

                    $this->refreshFormData(['status', 'payment_status']);
                }),

            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Upravovany zaznam ako Order ($this->record je v EditRecord typovany vseobecne).
     */
    private function getOrder(): Order
    {
        $record = $this->getRecord();
        assert($record instanceof Order);

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
