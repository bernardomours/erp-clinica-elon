<?php

namespace App\Filament\Resources\FinancialCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FinancialCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('clinic_id')
                    ->relationship('clinic', 'name')
                    ->visible(fn () => filament()->getCurrentPanel()->getId() === 'admin')
                    ->required(fn () => filament()->getCurrentPanel()->getId() === 'admin'),
                TextInput::make('name')
                    ->label('Nome da Categoria')
                    ->placeholder('Ex: Materiais de Consumo')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label('Tipo de Movimentação')
                    ->options([
                        'revenue' => 'Entrada (Receita)',
                        'expense' => 'Saída (Despesa)',
                    ])
                    ->required(),
                Toggle::make('is_active')
                    ->label('Ativo')
                    ->default(true),
            ]);
    }
}
