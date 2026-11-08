<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

namespace App\Filament\Resources\Banners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label(__('Image'))
                    ->disk('public')
                    ->height(60)
                    ->width(100),
                TextColumn::make('name')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('link_url')
                    ->label(__('Link'))
                    ->limit(30)
                    ->url(fn ($record) => $record->link_url, shouldOpenInNewTab: true)
                    ->placeholder('—'),
                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label(__('Order'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('Status'))
                    ->trueLabel('Active')
                    ->falseLabel('Unactive')
                    ->placeholder(__('All')),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
