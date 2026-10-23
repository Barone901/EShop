<?php

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
                Section::make('Basic information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Title')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('link_url')
                            ->label('URL link')
                            ->url()
                            ->placeholder('https://...')
                            ->maxLength(255)
                            ->helperText('If filled in, it takes precedence over the page.'),
                        Select::make('page_id')
                            ->label('Link to page')
                            ->relationship('page', 'title')
                            ->searchable()
                            ->preload()
                            ->helperText('Select the page to which the banner will link'),
                    ])->columns(2),

                Section::make('Image')
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('Banner image')
                            ->image()
                            ->required()
                            ->disk('public')
                            ->directory('banners')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                    ]),

                Section::make('Settings')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive banners are not displayed on the website'),
                        TextInput::make('sort_order')
                            ->label('Order')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->helperText('Lower number = higher in the list'),
                    ])->columns(2),
            ]);
    }
}
