<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

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
                Section::make(__('Content'))
                    ->columnSpan(2)
                    ->columns(1)
                    ->schema([
                        TextInput::make('title')
                            ->label(__('Title'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, callable $set, ?Page $record) {
                                if (!$record && $state) {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        TextInput::make('slug')
                            ->label(__('URL slug'))
                            ->required()
                            ->maxLength(255)
                            ->unique(Page::class, 'slug', ignoreRecord: true)
                            ->helperText(__('Automatically generated from the title')),

                        RichEditor::make('content')
                            ->label(__('Content'))
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
                            ->label(__('Featured image'))
                            ->image()
                            ->disk('public')
                            ->directory('pages')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                    ]),

                // Sidebar - 1 stĺpec
                Section::make(__('Settings'))
                    ->columnSpan(1)
                    ->schema([
                        Select::make('status')
                            ->label(__('Status'))
                            ->options(PageStatus::class)
                            ->default(PageStatus::Draft)
                            ->required(),

                        DateTimePicker::make('published_at')
                            ->label(__('Publish date'))
                            ->helperText(__('Leave empty to publish immediately')),

                        Toggle::make('is_featured')
                            ->label(__('Featured article'))
                            ->helperText(__('Shown among related articles')),

                        TextInput::make('sort_order')
                            ->label(__('Sort order'))
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ]),

                // SEO sekcia
                Section::make(__('SEO settings'))
                    ->columnSpanFull()
                    ->collapsed()
                    ->schema([
                        TextInput::make('meta_title')
                            ->label(__('Meta Title'))
                            ->maxLength(70)
                            ->helperText(__('If empty, the article title is used')),

                        Textarea::make('meta_description')
                            ->label(__('Meta Description'))
                            ->rows(2)
                            ->maxLength(160)
                            ->helperText(__('Short description for search engines (max 160 characters)')),

                        TextInput::make('meta_keywords')
                            ->label(__('Meta Keywords'))
                            ->maxLength(255)
                            ->helperText(__('Keywords separated by commas')),
                    ]),
            ]);
    }
}
