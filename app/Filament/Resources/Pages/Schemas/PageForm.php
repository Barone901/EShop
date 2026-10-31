<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Enums\PageStatus;
use App\Models\Page;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                // Hlavný obsah - 2 stĺpce
                Section::make('Content')
                    ->columnSpan(2)
                    ->columns(1)
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, callable $set, ?Page $record) {
                                if (!$record && $state) {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        TextInput::make('slug')
                            ->label('URL slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Page::class, 'slug', ignoreRecord: true)
                            ->helperText('Automaticly generated from the title'),



                        RichEditor::make('content')
                            ->label('Obsah')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('page-attachments'),

                        FileUpload::make('featured_image')
                            ->label('Hlavný obrázok')
                            ->image()
                            ->disk('public')
                            ->directory('pages')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                    ]),

                // Sidebar - 1 stĺpec
                Section::make('Nastavenia')
                    ->columnSpan(1)
                    ->schema([
                        Select::make('status')
                            ->label('Stav')
                            ->options(PageStatus::class)
                            ->default(PageStatus::Draft)
                            ->required(),

                        DateTimePicker::make('published_at')
                            ->label('Dátum publikovania')
                            ->helperText('Nechajte prázdne pre okamžité publikovanie'),

                        Toggle::make('is_featured')
                            ->label('Zvýraznená stránka')
                            ->helperText('Zobrazí sa na hlavnej stránke'),

                        TextInput::make('sort_order')
                            ->label('Poradie')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ]),

                // SEO sekcia
                Section::make('SEO nastavenia')
                    ->columnSpanFull()
                    ->collapsed()
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(70)
                            ->helperText('Ak je prázdne, použije sa názov stránky'),

                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->rows(2)
                            ->maxLength(160)
                            ->helperText('Krátky popis pre vyhľadávače (max 160 znakov)'),

                        TextInput::make('meta_keywords')
                            ->label('Meta Keywords')
                            ->maxLength(255)
                            ->helperText('Kľúčové slová oddelené čiarkou'),
                    ]),
            ]);
    }
}
