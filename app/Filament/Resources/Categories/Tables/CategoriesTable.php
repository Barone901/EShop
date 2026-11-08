<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label(__('Image'))
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder-category.png')),
                TextColumn::make('name')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parent.name')
                    ->label(__('Parent'))
                    ->searchable()
                    ->placeholder('—')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->searchable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('products_count')
                    ->label(__('Products'))
                    ->counts('products')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                TextColumn::make('sort_order')
                    ->label(__('Order'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                SelectFilter::make('parent_id')
                    ->label(__('Parent category'))
                    ->relationship('parent', 'name')
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
