<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes do Material')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome do Produto')
                            ->placeholder('Ex: Resina Z350, Luvas P, Gaze')
                            ->required()
                            ->maxLength(255),

                        Select::make('unit')
                            ->label('Unidade de Medida')
                            ->options([
                                'un' => 'Unidade (un)',
                                'cx' => 'Caixa (cx)',
                                'pct' => 'Pacote (pct)',
                                'kg' => 'Quilograma (kg)',
                                'g' => 'Grama (g)',
                                'l' => 'Litro (l)',
                                'ml' => 'Mililitro (ml)',
                                'tb' => 'Tubo/Seringa (tb)',
                            ])
                            ->default('un')
                            ->required(),
                    ])->columns(2),
                    Section::make('Controle de Estoque')
                    ->schema([
                        TextInput::make('current_stock')
                            ->label('Estoque Atual')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated()
                            ->helperText('O estoque é atualizado automaticamente nas compras e consumos.'),

                        TextInput::make('minimum_stock')
                            ->label('Estoque Mínimo (Alerta)')
                            ->numeric()
                            ->default(5)
                            ->required()
                            ->helperText('O sistema avisará quando a quantidade chegar neste número.'),
                    ])->columns(2),
                Select::make('clinic_id')
                    ->visible(fn () => filament()->getCurrentPanel()->getId() === 'admin')
                    ->required(fn () => filament()->getCurrentPanel()->getId() === 'admin')
                    ->relationship('clinic', 'name')
                    ->label('Clínica'),
            ]);
    }
}
