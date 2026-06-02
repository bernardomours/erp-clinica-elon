<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\RestoreBulkAction; 
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\TrashedFilter; 
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Facades\Filament;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record): string => $record->cpf_cnpj ?? 'Sem documento')
                    ->copyable(),

                TextColumn::make('phone')
                    ->label('Contato')
                    ->icon('heroicon-m-phone')
                    ->iconColor('primary')
                    ->weight('medium')
                    ->searchable(['phone', 'email']) 
                    ->description(fn ($record): string => $record->email ?? 'Sem e-mail'),

                TextColumn::make('birth_date')
                    ->label('Nascimento')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('full_address')
                    ->label('Endereço')
                    ->getStateUsing(function ($record) {
                        if (!$record->street) return '-';
                        
                        $complemento = $record->complement ? " ({$record->complement})" : '';
                        
                        return "{$record->street}, {$record->number}{$complemento} - {$record->neighborhood}, {$record->city}/{$record->state}";
                    })
                    ->wrap()
                    ->searchable(['street', 'neighborhood', 'city'])
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Ativo' : 'Inativo')
                    ->color(fn ($state): string => $state ? 'success' : 'danger'),
                
                TextColumn::make('clinic.name')
                    ->label('Clínica')
                    ->badge()
                    ->visible(fn () => Filament::getCurrentPanel()->getId() === 'admin'),

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
                    
                TextColumn::make('deleted_at')
                    ->label('Excluído em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                //EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}