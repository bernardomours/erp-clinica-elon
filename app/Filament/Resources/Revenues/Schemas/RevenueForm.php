<?php

namespace App\Filament\Resources\Revenues\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;

class RevenueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Identificação')
                        ->description('Vincule ao paciente')
                        ->icon('heroicon-m-user')
                        ->schema([
                            Select::make('clinic_id')
                                ->relationship('clinic', 'name')
                                ->label('Clínica')
                                ->visible(fn () => filament()->getCurrentPanel()->getId() === 'admin')
                                ->required(fn () => filament()->getCurrentPanel()->getId() === 'admin'),

                            Select::make('customer_id')
                                ->relationship('customer', 'name')
                                ->label('Paciente')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),

                    Step::make('Valor')
                        ->description('Defina o montante e a forma')
                        ->icon('heroicon-m-currency-dollar')
                        ->schema([
                            Select::make('payment_plan')
                                ->label('Plano de Pagamento')
                                ->options([
                                    'Boleto' => 'Boleto Bancário',
                                    'Cartão de Crédito' => 'Cartão de Crédito',
                                    'Carnê da Clínica' => 'Carnê da Clínica',
                                    'PIX' => 'PIX (À vista)',
                                    'Dinheiro' => 'Dinheiro (À vista)',
                                ])
                                ->required(),

                            TextInput::make('total_amount')
                                ->label('Valor Total (R$)')
                                ->required()
                                ->numeric()
                                ->prefix('R$')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($get, $set) {
                                    $total = floatval($get('total_amount') ?? 0);
                                    $installments = intval($get('installments') ?? 1);
                                    if ($installments > 0) {
                                        $set('installment_amount', round($total / $installments, 2));
                                    }
                                }),
                        ]),

                    Step::make('Parcelamento')
                        ->description('Configure as parcelas')
                        ->icon('heroicon-m-calculator')
                        ->schema([
                            TextInput::make('installments')
                                ->label('Quantidade de Parcelas')
                                ->required()
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($get, $set) {
                                    $total = floatval($get('total_amount') ?? 0);
                                    $installments = intval($get('installments') ?? 1);
                                    if ($installments > 0) {
                                        $set('installment_amount', round($total / $installments, 2));
                                    }
                                }),

                            TextInput::make('installment_amount')
                                ->label('Valor da Parcela (R$)')
                                ->required()
                                ->numeric()
                                ->prefix('R$')
                                ->readOnly(),

                            Select::make('status')
                                ->label('Status Inicial')
                                ->options([
                                    'pending' => 'Pendente',
                                    'paid' => 'Pago',
                                    'late' => 'Atrasado',
                                ])
                                ->default('pending')
                                ->required(),
                        ]),
                ])
                ->columnSpanFull()
                ->skippable(),
            ]);
    }
}
