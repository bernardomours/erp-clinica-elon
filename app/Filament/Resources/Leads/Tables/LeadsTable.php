<?php

namespace App\Filament\Resources\Leads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('name')
                ->label('Nome')
                ->searchable()
                ->sortable(),
                
            TextColumn::make('email')
                ->label('E-mail')
                ->searchable(),
                
            TextColumn::make('phone')
                ->label('Telefone / WhatsApp'),
                
            TextColumn::make('created_at')
                ->label('Data de Interesse')
                ->dateTime('d/m/Y H:i')
                ->sortable(),
        ])
            ->filters([
                //
            ])
            ->recordActions([
                //EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
