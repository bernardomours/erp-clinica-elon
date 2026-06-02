<?php

namespace App\Filament\Resources\ProductPurchases\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;

class ProductPurchaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes da Nota / Compra')
                    ->schema([
                       Select::make('product_id')
                            ->relationship('product', 'name')
                            ->label('Produto')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->label('Fornecedor')
                            ->searchable()
                            ->required()
                            ->preload(),

                        DatePicker::make('purchase_date')
                            ->label('Data da Compra')
                            ->default(now())
                            ->required(),
                    ])->columns(3),

                Section::make('Quantidades e Valores')
                    ->schema([
                        TextInput::make('quantity')
                            ->label('Quantidade Comprada')
                            ->numeric()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($set, $get) {
                                $set('total_cost', floatval($get('quantity')) * floatval($get('unit_cost')));
                            }),

                       TextInput::make('unit_cost')
                            ->label('Custo Unitário')
                            ->numeric()
                            ->prefix('R$')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($set, $get) {
                                $set('total_cost', floatval($get('quantity')) * floatval($get('unit_cost')));
                            }),

                        TextInput::make('total_cost')
                            ->label('Valor Total (NF)')
                            ->numeric()
                            ->prefix('R$')
                            ->readOnly()
                            ->required(),
                    ])->columns(3),

                Section::make('Rastreabilidade')
                    ->schema([
                        TextInput::make('batch')
                            ->label('Lote do Fabricante')
                            ->maxLength(255),

                        DatePicker::make('expiration_date')
                            ->label('Data de Validade'),
                    ])->columns(2),

                Section::make('Integração Financeira (Gerar Contas a Pagar)')
                    ->description('Preencha os dados abaixo para o sistema lançar esta compra automaticamente nas suas Despesas.')
                    ->schema([
                        Select::make('payment_plan')
                            ->label('Forma de Pagamento')
                            ->options([
                                'Boleto' => 'Boleto Bancário',
                                'PIX' => 'PIX',
                                'Cartão' => 'Cartão de Crédito',
                                'Dinheiro' => 'Dinheiro',
                            ])
                            ->default('PIX')
                            ->live()
                            ->afterStateUpdated(function ($set, $state) {
                                if (in_array($state, ['PIX', 'Dinheiro'])) {
                                    $set('installments', 1);
                                    $set('status', 'paid');
                                } else {
                                    $set('status', 'pending');
                                }
                            })
                            ->dehydrated(false)
                            ->required(),

                        TextInput::make('installments')
                            ->label('Qtd. Parcelas')
                            ->numeric()
                            ->default(1)
                            ->dehydrated(false)
                            ->required()
                            ->visible(fn ($get): bool => in_array($get('payment_plan'), ['Boleto', 'Cartão'])),

                        DatePicker::make('due_date')
                            ->label(fn ($get): string => in_array($get('payment_plan'), ['Boleto', 'Cartão']) ? 'Vencimento (1ª Parcela)' : 'Data do Pagamento')
                            ->default(now())
                            ->dehydrated(false)
                            ->required(),

                        Select::make('status')
                            ->label('Situação')
                            ->options([
                                'pending' => 'Pendente',
                                'paid' => 'Já Pago',
                            ])
                            ->default('pending')
                            ->dehydrated(false)
                            ->required(),
                    ])->columns(4),
            ]);
    }
}
