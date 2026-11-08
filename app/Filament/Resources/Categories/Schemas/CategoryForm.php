<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make(__('Basic information'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Title'))
                            ->required()
                            ->maxLength(150)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->label(__('URL slug'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(3)
                            ->columnSpanFull(),
                        FileUpload::make('image_url')
                            ->label(__('Category Image'))
                            ->image()
                            ->disk('public')
                            ->directory('categories')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make(__('Hierarchy'))
                    ->schema([
                        Select::make('parent_id')
                            ->label(__('Parent category'))
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder(__('None (main category)')),
                        TextInput::make('sort_order')
                            ->label(__('Order'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->helperText(__('Lower number = higher in the list')),
                        Toggle::make('is_active')
                            ->label(__('Active'))
                            ->default(true)
                            ->helperText(__('Inactive categories will not be displayed')),
                    ])->columns(2),
            ]);
    }
}
