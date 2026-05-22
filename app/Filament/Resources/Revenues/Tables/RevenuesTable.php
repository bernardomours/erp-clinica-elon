<?php

namespace App\Filament\Resources\Revenues\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class RevenuesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('clinic.name')
                    ->label('Clínica')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->visible(fn () => filament()->getCurrentPanel()->getId() === 'admin'),

                TextColumn::make('customer.name')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('description')
                    ->label('Descrição / Origem')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->color('gray'),

                TextColumn::make('total_amount')
                    ->label('Valor Total')
                    ->money('BRL')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),

                TextColumn::make('installments')
                    ->label('Parcelas')
                    ->formatStateUsing(fn ($state) => $state . 'x')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('installment_amount')
                    ->label('Valor da Parcela')
                    ->money('BRL')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('payment_plan')
                    ->label('Forma de Pag.')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('status')
                    ->label('Situação')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid' => 'Pago',
                        'pending' => 'Pendente',
                        'cancelled' => 'Cancelado',
                        'refunded' => 'Estornado',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'cancelled', 'refunded' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Data do Faturamento')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('updated_at')
                    ->label('Registrado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Excluído em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
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
