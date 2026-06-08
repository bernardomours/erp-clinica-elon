<?php

namespace App\Filament\Resources\Clinics\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Str;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Http;

class ClinicForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
            Section::make('Dados da Assinatura')
                ->description('Informações principais da clínica inquilina.')
                ->icon('heroicon-o-building-office')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Nome da Clínica')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, ?string $state) => $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->label('URL Amigável (Slug)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->readOnly(),

                        TextInput::make('tax_id')
                            ->label('CNPJ')
                            ->mask('99.999.999/0009-99')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        DatePicker::make('expires_at')
                            ->label('Vencimento do Contrato')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->helperText('A partir desta data, o sistema poderá restringir o acesso.'),

                        Select::make('signature_status')
                            ->label('Status da Assinatura')
                            ->options([
                                'active' => 'Ativa / Em Dia',
                                'expired' => 'Vencida (Inadimplente)',
                                'blocked' => 'Bloqueada (Acesso Negado)',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false),
                    ]),
                ]),

                Section::make('Endereço da Sede')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('zip_code')
                            ->label('CEP')
                            ->mask('99999-999')
                            ->dehydrateStateUsing(fn ($state) => str_replace('-', '', $state))
                            ->maxLength(9)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($set, $state) {
                                if (!$state) return;
                                $cep = preg_replace('/[^0-9]/', '', $state);
                                $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");
                                if ($response->successful()) {
                                    $data = $response->json();
                                    $set('street', $data['logradouro'] ?? '');
                                    $set('neighborhood', $data['bairro'] ?? '');
                                    $set('city', $data['localidade'] ?? '');
                                    $set('state', $data['uf'] ?? '');
                                }
                            }),

                        TextInput::make('street')->label('Logradouro')->columnSpanFull(),
                        TextInput::make('number')->label('Número')->required(),
                        TextInput::make('neighborhood')->label('Bairro'),
                        TextInput::make('city')->label('Cidade'),
                        TextInput::make('state')->label('UF')->maxLength(2),
                        TextInput::make('complement')->label('Complemento'),
                    ]),
                ]),

                Section::make('Módulos Contratados')
                    ->description('Ative ou desative o acesso da clínica aos módulos do sistema.')
                    ->schema([
                        Toggle::make('has_scheduling')
                            ->label('Módulo de Agendamento (Agenda e Pacientes)')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true),

                        Toggle::make('has_financial')
                            ->label('Módulo Financeiro (Receitas, Despesas e Relatórios)')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(false),
                    ])->columns(2),
        ]);
    }
}
