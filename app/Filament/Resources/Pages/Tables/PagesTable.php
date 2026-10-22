<?php

namespace App\Filament\Resources\Pages\Tables;

use App\Models\Page;
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
                    ->label('Image')
                    ->disk('public')
                    ->height(50)
                    ->width(80)
                    ->placeholder('—'),

                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('slug')
                    ->label('URL')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => Page::STATUS_DRAFT,
                        'success' => Page::STATUS_PUBLISHED,
                        'gray' => Page::STATUS_ARCHIVED,
                    ])
                    ->formatStateUsing(fn (string $state) => Page::getStatuses()[$state] ?? $state)
                    ->sortable(),

                TextColumn::make('author.name')
                    ->label('Autor')
                    ->placeholder('—')
                    ->toggleable(),

                IconColumn::make('is_featured')
                    ->label('Highlighted')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('views_count')
                    ->label('Display')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('Unpublished'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Page::getStatuses()),

                TernaryFilter::make('is_featured')
                    ->label('Highlighted')
                    ->trueLabel('Yes')
                    ->falseLabel('No')
                    ->placeholder('All'),

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

