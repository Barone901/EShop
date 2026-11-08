<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Basic information'))
                    ->schema([
                        TextInput::make('order_number')
                            ->label(__('Order number'))
                            ->disabled(),
                        Select::make('status')
                            ->label(__('Order status'))
                            ->options(OrderStatus::class)
                            // Stav sa meni len tlacidlami na stranke (OrderService) - pravidla prechodov a sklad
                            ->disabled(),
                        Select::make('payment_status')
                            ->label(__('Payment status'))
                            ->options(PaymentStatus::class)
                            // Stav sa meni len tlacidlami na stranke (OrderService) - pravidla prechodov a sklad
                            ->disabled(),
                        Select::make('payment_method')
                            ->label(__('Payment method'))
                            ->options(PaymentMethod::class)
                            ->disabled(),
                    ])->columns(2),

                Section::make(__('Delivery details'))
                    ->relationship('shippingAddress')
                    ->schema([
                        TextInput::make('first_name')->label(__('First name'))->disabled(),
                        TextInput::make('last_name')->label(__('Last name'))->disabled(),
                        TextInput::make('email')->label(__('Email'))->disabled(),
                        TextInput::make('phone')->label(__('Phone'))->disabled(),
                        TextInput::make('street')->label(__('Address'))->disabled(),
                        TextInput::make('city')->label(__('City'))->disabled(),
                        TextInput::make('postal_code')->label(__('Postal code'))->disabled(),
                        TextInput::make('country')->label(__('Country'))->disabled(),
                    ])
                    ->columns(2),

                Section::make(__('Billing details'))
                    ->relationship('billingAddress')
                    ->schema([
                        TextInput::make('first_name')->label(__('First name'))->disabled(),
                        TextInput::make('last_name')->label(__('Last name'))->disabled(),
                        TextInput::make('email')->label(__('Email'))->disabled(),
                        TextInput::make('phone')->label(__('Phone'))->disabled(),
                        TextInput::make('street')->label(__('Address'))->disabled(),
                        TextInput::make('city')->label(__('City'))->disabled(),
                        TextInput::make('postal_code')->label(__('Postal code'))->disabled(),
                        TextInput::make('country')->label(__('Country'))->disabled(),

                        TextInput::make('company_name')->label(__('Company'))->disabled(),
                        TextInput::make('ico')->label(__('Company ID'))->disabled(),
                        TextInput::make('dic')->label(__('Tax ID'))->disabled(),
                        TextInput::make('ic_dph')->label(__('VAT ID'))->disabled(),
                    ])
                    ->columns(2),
                Section::make(__('Price data'))
                    ->schema([
                        TextInput::make('subtotal')
                            ->label(__('Subtotal'))
                            ->disabled()
                            ->suffix('€'),
                        TextInput::make('shipping_cost')
                            ->label(__('Shipping'))
                            ->disabled()
                            ->suffix('€'),
                        TextInput::make('total')
                            ->label(__('Total'))
                            ->disabled()
                            ->suffix('€'),
                    ])->columns(3),

                Section::make(__('Note'))
                    ->schema([
                        Textarea::make('note')
                            ->label(__('Note to the order'))
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
