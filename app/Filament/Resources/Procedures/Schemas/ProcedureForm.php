<?php

namespace App\Filament\Resources\Procedures\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;

class ProcedureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes do Procedimento')
                    ->description('Informações básicas e precificação.')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Select::make('clinic_id')
                            ->relationship('clinic', 'name')
                            ->label('Clínica')
                            ->visible(fn () => filament()->getCurrentPanel()->getId() === 'admin')
                            ->required(fn () => filament()->getCurrentPanel()->getId() === 'admin')
                            ->columnSpanFull(),

                        TextInput::make('name')
                            ->label('Nome do Procedimento')
                            ->placeholder('Ex: Limpeza Profilática, Extração...')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        TextInput::make('base_price')
                            ->label('Preço Base')
                            ->required()
                            ->numeric()
                            ->prefix('R$')
                            ->placeholder('0.00')
                            ->columnSpan(1),

                        Toggle::make('is_active')
                            ->label('Ativo no Sistema')
                            ->default(true)
                            ->inline(false)
                            ->columnSpan(1),
                    ])->columns(4),

                Section::make('Receita de Materiais (Consumo Automático)')
                    ->description('Selecione os produtos e as quantidades que este procedimento consome por padrão ao ser realizado.')
                    ->icon('heroicon-o-beaker')
                    ->schema([
                        Repeater::make('procedureProducts')
                            ->relationship()
                            ->label('') 
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'name') 
                                    ->label('Material / Produto')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                TextInput::make('quantity')
                                    ->label('Quantidade Utilizada')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(0.01)
                                    ->required(),
                            ])
                            ->columns(2)
                            ->createItemButtonLabel('+ Adicionar Material à Receita')
                    ]),
            ]);
    }
}