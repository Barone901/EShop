<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(200)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->label(__('URL slug'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Textarea::make('description')
                    ->label(__('Description'))
                    ->rows(4)
                    ->columnSpanFull(),
                Select::make('category_id')
                    ->label(__('Category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('price')
                    ->label(__('Price'))
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->suffix('€'),
                TextInput::make('stock')
                    ->label(__('In stock'))
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                FileUpload::make('image_url')
                    ->label(__('Image'))
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
