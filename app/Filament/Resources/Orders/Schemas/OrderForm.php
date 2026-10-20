<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
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
                Section::make('Basic information')
                    ->schema([
                        TextInput::make('order_number')
                            ->label('Order number')
                            ->disabled(),
                        Select::make('status')
                            ->label('Order status')
                            ->options(Order::STATUSES)
                            ->required(),
                        Select::make('payment_status')
                            ->label('Payment status')
                            ->options(Order::PAYMENT_STATUSES)
                            ->required(),
                        Select::make('payment_method')
                            ->label('Payment method')
                            ->options(Order::PAYMENT_METHODS)
                            ->disabled(),
                    ])->columns(2),

                Section::make('Delivery details')
                    ->relationship('shippingAddress')
                    ->schema([
                        TextInput::make('first_name')->label('First name')->disabled(),
                        TextInput::make('last_name')->label('Last name')->disabled(),
                        TextInput::make('email')->label('Email')->disabled(),
                        TextInput::make('phone')->label('Phone')->disabled(),
                        TextInput::make('street')->label('Address')->disabled(),
                        TextInput::make('city')->label('City')->disabled(),
                        TextInput::make('postal_code')->label('Postal code')->disabled(),
                        TextInput::make('country')->label('Country')->disabled(),
                    ])
                    ->columns(2),


                Section::make('Billing details')
                    ->relationship('billingAddress')
                    ->schema([
                        TextInput::make('first_name')->label('First name')->disabled(),
                        TextInput::make('last_name')->label('Last name')->disabled(),
                        TextInput::make('email')->label('Email')->disabled(),
                        TextInput::make('phone')->label('Phone')->disabled(),
                        TextInput::make('street')->label('Address')->disabled(),
                        TextInput::make('city')->label('City')->disabled(),
                        TextInput::make('postal_code')->label('Postal code')->disabled(),
                        TextInput::make('country')->label('Country')->disabled(),

                        TextInput::make('company_name')->label('Company')->disabled(),
                        TextInput::make('ico')->label('IČO')->disabled(),
                        TextInput::make('dic')->label('DIČ')->disabled(),
                        TextInput::make('ic_dph')->label('IČ DPH')->disabled(),
                    ])
                    ->columns(2),
                Section::make('Price data')
                    ->schema([
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->disabled()
                            ->suffix('€'),
                        TextInput::make('shipping_cost')
                            ->label('Shipping')
                            ->disabled()
                            ->suffix('€'),
                        TextInput::make('total')
                            ->label('Total')
                            ->disabled()
                            ->suffix('€'),
                    ])->columns(3),

                Section::make('Note')
                    ->schema([
                        Textarea::make('note')
                            ->label('Note to the order')
                            ->disabled()
                            ->columnSpanFull(),
                    ])
            ]);
    }
}

