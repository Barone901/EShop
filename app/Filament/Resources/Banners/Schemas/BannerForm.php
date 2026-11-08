<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BannerForm
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
                            ->maxLength(255),
                        TextInput::make('link_url')
                            ->label(__('URL link'))
                            ->url()
                            ->placeholder(__('https://...'))
                            ->maxLength(255)
                            ->helperText(__('If filled in, it takes precedence over the page.')),
                        Select::make('page_id')
                            ->label(__('Link to page'))
                            ->relationship('page', 'title')
                            ->searchable()
                            ->preload()
                            ->helperText(__('Select the page to which the banner will link')),
                    ])->columns(2),

                Section::make(__('Image'))
                    ->schema([
                        FileUpload::make('image_path')
                            ->label(__('Banner image'))
                            ->image()
                            ->required()
                            ->disk('public')
                            ->directory('banners')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                    ]),

                Section::make(__('Settings'))
                    ->schema([
                        Toggle::make('is_active')
                            ->label(__('Active'))
                            ->default(true)
                            ->helperText(__('Inactive banners are not displayed on the website')),
                        TextInput::make('sort_order')
                            ->label(__('Order'))
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->helperText(__('Lower number = higher in the list')),
                    ])->columns(2),
            ]);
    }
}
