<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Produto')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('unit')
                    ->label('Unidade')
                    ->badge()
                    ->color('info'),

                TextColumn::make('current_stock')
                    ->label('Estoque Atual')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->current_stock <= $record->minimum_stock ? 'danger' : 'success')
                    ->description(fn ($record) => $record->current_stock <= $record->minimum_stock ? 'Comprar mais!' : ''),

                TextColumn::make('minimum_stock')
                    ->label('Estoque Mínimo')
                    ->numeric(),
            ])
            ->filters([
                TrashedFilter::make(),
                Filter::make('estoque_baixo')
                    ->label('Estoque Baixo')
                    ->query(fn (Builder $query): Builder => $query->whereColumn('current_stock', '<=', 'minimum_stock'))
                    ->toggle(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
