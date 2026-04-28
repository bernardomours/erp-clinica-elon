<?php

namespace App\Filament\Resources\PatientSchedules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PatientSchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('clinic.name')
                    ->label('Clínica')
                    ->badge()
                    ->color('info')
                    ->visible(fn () => filament()->getCurrentPanel()->getId() === 'admin'),
                TextColumn::make('customer.name')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('schedule_date')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('procedure')
                    ->label('Procedimento')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Situação')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Scheduled' => 'Agendada',
                        'Confirmed' => 'Confirmada',
                        'Completed' => 'Realizada',
                        'Cancelled' => 'Cancelada',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Scheduled' => 'warning', // Amarelo
                        'Confirmed' => 'info',    // Azul
                        'Completed' => 'success', // Verde
                        'Cancelled' => 'danger',  // Vermelho
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
