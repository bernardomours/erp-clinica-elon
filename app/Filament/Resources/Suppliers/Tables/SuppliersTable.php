<?php

namespace App\Filament\Resources\Suppliers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Fornecedor')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->cpf_cnpj ?: 'Documento não informado'),

                TextColumn::make('phone')
                    ->label('Contato')
                    ->searchable()
                    ->icon('heroicon-m-phone')
                    ->color('info')
                    ->default('-')
                    ->searchable(['phone', 'email']) 
                    ->description(fn ($record): string => $record->email ?? 'Sem e-mail'),

                TextColumn::make('street')
                    ->label('Endereço')
                    ->state(function ($record) {
                        $endereco = $record->street;
                        if ($record->number) $endereco .= ", {$record->number}";
                        if ($record->complement) $endereco .= " - {$record->complement}";
                        if ($record->neighborhood) $endereco .= " - {$record->neighborhood}";
                        if ($record->city && $record->state) $endereco .= ", {$record->city}/{$record->state}";
                        
                        return $endereco ?: 'Endereço não cadastrado';
                    })
                    ->searchable(['street', 'neighborhood', 'city', 'state'])
                    ->wrap(),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state) => $state ? 'Ativo' : 'Inativo')
                    ->color(fn (bool $state) => $state ? 'success' : 'danger'),


                TextColumn::make('clinic.name')
                    ->label('Clínica')
                    ->badge()
                    ->color('info')
                    ->sortable(),

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
                    ->label('Deletado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
