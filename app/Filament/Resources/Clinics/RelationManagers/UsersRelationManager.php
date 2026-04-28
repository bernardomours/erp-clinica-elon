<?php

namespace App\Filament\Resources\Clinics\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome Completo')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('E-mail (Login)')
                    ->email()
                    ->required()
                    ->maxLength(255),

                TextInput::make('password')
                    ->label('Senha')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(255),

                Select::make('role')
                    ->label('Nível de Acesso')
                    ->options([
                        'clinic_admin' => 'Administrativo',
                        'operator' => 'Auxiliar',
                    ])
                    ->default('operator')
                    ->required()
                    ->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->heading('Equipe da Clínica')
            ->description('Gerencie os usuários que têm acesso a este consultório.')
            ->columns([
                TextColumn::make('name')
                    ->label('Usuário')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('E-mail')
                    ->icon('heroicon-m-envelope')
                    ->searchable(),
                
                TextColumn::make('role')
                    ->label('Cargo/Função')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'clinic_admin' => 'Líder',
                        'operator' => 'Auxiliar',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'clinic_admin' => 'primary',
                        'operator' => 'gray',
                        default => 'gray',
                    })
                    ->searchable(),
            ])
            ->filters([
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Novo Usuário')
                    ->icon('heroicon-m-plus'),

                AttachAction::make()
                    ->label('Vincular Existente')
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                EditAction::make(),
                
                DetachAction::make()
                    ->label('Remover da Equipe'), 
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}