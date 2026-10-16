<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(200)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->label('URL slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Description')
                    ->rows(4)
                    ->columnSpanFull(),
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('price')
                    ->label('Price')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->suffix('€'),
                TextInput::make('stock')
                    ->label('In stock')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                FileUpload::make('image_url')
                    ->label('Image')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public')
                    ->imageEditor()
                    ->maxSize(2048)
                    ->columnSpanFull(),
            ]);
    }
}
