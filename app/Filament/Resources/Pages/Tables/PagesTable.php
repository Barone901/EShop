<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

namespace App\Filament\Resources\Pages\Tables;

use App\Enums\PageStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')
                    ->label(__('Image'))
                    ->disk('public')
                    ->height(50)
                    ->width(80)
                    ->placeholder('—'),

                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('slug')
                    ->label(__('URL'))
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Popisok a farbu badge berie Filament z enumu PageStatus
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('author.name')
                    ->label(__('Author'))
                    ->placeholder('—')
                    ->toggleable(),

                IconColumn::make('is_featured')
                    ->label(__('Highlighted'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('views_count')
                    ->label(__('Display'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('published_at')
                    ->label(__('Published'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder(__('Unpublished')),

                TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(PageStatus::class),

                TernaryFilter::make('is_featured')
                    ->label(__('Highlighted'))
                    ->trueLabel('Yes')
                    ->falseLabel('No')
                    ->placeholder(__('All')),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
