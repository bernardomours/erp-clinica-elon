<?php

namespace App\Filament\Resources\Procedures\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Table;
use Filament\Facades\Filament;

class ProceduresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('clinic.name')
                    ->label('Clínica')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => Filament::getCurrentPanel()->getId() === 'admin'),

                TextColumn::make('name')
                    ->label('Procedimento')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('base_price')
                    ->label('Preço Base')
                    ->money('BRL')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),

                TextColumn::make('materiais_list')
                    ->label('Materiais (Receita)')
                    ->getStateUsing(fn ($record) => $record->procedureProducts()->count() . ' item(ns)')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-eye')
                    ->action(
                        Action::make('view_materials')
                            ->modalHeading(fn ($record) => 'Procedimento: ' . $record->name)
                            ->modalDescription('Lista de materiais consumidos automaticamente por este procedimento.')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Fechar')
                            ->infolist([
                                RepeatableEntry::make('procedureProducts')
                                    ->label('Produto | Quantidade')
                                    ->schema([
                                        TextEntry::make('product.name')
                                            ->label('Material / Produto')
                                            ->weight('bold'),
                                            
                                        TextEntry::make('quantity')
                                            ->label('Quantidade Gasta')
                                            ->badge()
                                            ->color('warning'),
                                    ])
                                    ->columns(2)
                            ])
                    ),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name', 'asc')
            ->filters([
                //
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
