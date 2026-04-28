<?php

namespace App\Filament\Resources\Clinics\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClinicsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Clínica')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record): string => $record->tax_id ?? 'CNPJ não informado'),

                TextColumn::make('signature_status')
                    ->label('Status do Contrato')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'trial' => 'Período de Teste',
                        'active' => 'Ativa',
                        'expired' => 'Vencida',
                        'blocked' => 'Bloqueada',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'trial' => 'info',
                        'expired' => 'warning',
                        'blocked' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('expires_at')
                    ->label('Vencimento')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => 
                        $record->expires_at && $record->expires_at->isPast() ? 'danger' : null
                    ),

                TextColumn::make('days_overdue')
                    ->label('Atraso')
                    ->getStateUsing(function ($record) {
                        if (!$record->expires_at || $record->expires_at->isFuture()) {
                            return 'Em dia';
                        }
                        
                        $days = $record->expires_at->diffInDays(now()->startOfDay());
                        return "{$days} dias";
                    })
                    ->color(fn ($state) => $state === 'Em dia' ? 'success' : 'danger')
                    ->icon(fn ($state) => $state === 'Em dia' ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle'),

                TextColumn::make('full_address')
                    ->label('Localização')
                    ->getStateUsing(function ($record) {
                        if (!$record->street) return 'Endereço não cadastrado';
                        
                        $complemento = $record->complement ? " - {$record->complement}" : '';
                        
                        return "{$record->street}, {$record->number}{$complemento} | {$record->neighborhood} - {$record->city}/{$record->state}";
                    })
                    ->wrap()
                    ->searchable(['street', 'city', 'zip_code'])
                    ->color('gray')
                    ->size('sm'),
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
