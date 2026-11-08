<?php

// [AI] Upravene s pomocou AI (Claude).

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
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
            $this->advanceAction('confirm', OrderStatus::Confirmed, 'Confirm', 'heroicon-o-check', 'info'),
            $this->advanceAction('process', OrderStatus::Processing, 'Process', 'heroicon-o-cog', 'primary'),
            $this->advanceAction('ship', OrderStatus::Shipped, 'Ship', 'heroicon-o-truck', 'purple'),
            $this->advanceAction('deliver', OrderStatus::Delivered, 'Mark Delivered', 'heroicon-o-check-circle', 'success'),

            Actions\Action::make('cancel')
                ->label(__('Cancel'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('Cancel Order'))
                ->modalDescription(__('Are you sure you want to cancel this order? This will return products to stock.'))
                ->visible(fn (): bool => $this->getOrder()->status->isCancellableByAdmin())
                // Rovnake rusenie ako u zakaznika (transakcia, zamok, vratenie na sklad)
                ->action(fn () => $this->runOrderAction(
                    fn (OrderService $orders) => $orders->cancelOrder($this->getOrder(), byAdmin: true)
                )),

            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Tlacidlo posunu objednavky na dalsi stav. Ci je prechod povoleny (aj pravidlo
     * "dobierka je pri doruceni zaplatena"), rozhoduje OrderService/OrderStatus, nie tato stranka.
     */
    private function advanceAction(string $name, OrderStatus $status, string $label, string $icon, string $color): Actions\Action
    {
        return Actions\Action::make($name)
            ->label(__($label))
            ->icon($icon)
            ->color($color)
            ->requiresConfirmation()
            ->visible(fn (): bool => $this->getOrder()->status->canAdvanceTo($status))
            ->action(fn () => $this->runOrderAction(
                fn (OrderService $orders) => $orders->advanceStatus($this->getOrder(), $status)
            ));
    }

    /**
     * Spusti akciu sluzby a chybu (OrderException) zobrazi ako notifikaciu Filamentu.
     * Livewire akcia nema klasicku HTTP odpoved, preto sa tu OrderException::render() nepouzije.
     */
    private function runOrderAction(\Closure $action): void
    {
        try {
            $action(app(OrderService::class));
        } catch (OrderException $e) {
            Notification::make()->title($e->getMessage())->danger()->send();
        }

        $this->refreshFormData(['status', 'payment_status']);
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
