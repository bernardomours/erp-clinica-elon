<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Grid;
use Filament\Support\RawJs;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Http;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Dados Pessoais')
                        ->label('Dados Pessoais')
                        ->description('Informações de identificação e contato do cliente.')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Grid::make(2)->schema([
                                Select::make('clinic_id')
                                    ->relationship('clinic', 'name')
                                    ->label('Clínica')
                                    ->visible(fn () => filament()->getCurrentPanel()->getId() === 'admin')
                                    ->required(fn () => filament()->getCurrentPanel()->getId() === 'admin'),
                                TextInput::make('name')
                                    ->label('Nome Completo')
                                    ->required()
                                    ->maxLength(255),
                                    
                                TextInput::make('cpf_cnpj')
                                    ->label('CPF/CNPJ')
                                    ->required()
                                    ->maxLength(18)
                                    ->mask(RawJs::make(<<<'JS'
                                        $input.length > 14 ? '99.999.999/9999-99' : '999.999.999-99'
                                    JS))
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'Esse CPF ou CNPJ já está cadastrado.'
                                    ]),
                                    
                                DatePicker::make('birth_date')
                                    ->label('Data de Nascimento')
                                    ->displayFormat('d/m/Y'),
                                    
                                TextInput::make('phone')
                                    ->label('Telefone/WhatsApp')
                                    ->mask('(99) 99999-9999')
                                    ->tel()
                                    ->required(),

                                TextInput::make('email')
                                        ->label('Email')
                                        ->nullable(),
                            ]),
                            
                            Toggle::make('is_active')
                                ->label('Cadastro Ativo?')
                                ->default(true)
                                ->inline(false),
                        ]),

                    Step::make('Adress')
                        ->label('Endereço')
                        ->description('Dados de localização.')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            Grid::make(3)->schema([
                                TextInput::make('zip_code')
                                    ->label('CEP')
                                    ->required()
                                    ->mask('99999-999')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($set, ?string $state) {
                                        if (blank($state)) return;

                                        // Remove o traço para consultar a API
                                        $cep = preg_replace('/[^0-9]/', '', $state);
                                        if (strlen($cep) !== 8) return;

                                        // Consulta a API do ViaCEP
                                        $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");

                                        if ($response->successful() && !isset($response['erro'])) {
                                            $data = $response->json();
                                            
                                            // Preenche os outros campos instantaneamente
                                            $set('street', $data['logradouro'] ?? null);
                                            $set('neighborhood', $data['bairro'] ?? null);
                                            $set('city', $data['localidade'] ?? null);
                                            $set('state', $data['uf'] ?? null);
                                        }
                                    }),

                                TextInput::make('street')
                                    ->label('Rua/Logradouro')
                                    ->helperText('Preenchido Automaticamente')
                                    ->required(),

                                TextInput::make('number')
                                    ->label('Número')
                                    ->required(),

                                TextInput::make('complement')
                                    ->label('Complemento')
                                    ->columnSpanFull(),

                                TextInput::make('neighborhood')
                                    ->label('Bairro')
                                    ->required(),

                                TextInput::make('city')
                                    ->label('Cidade')
                                    ->required(),

                                TextInput::make('state')
                                    ->label('Estado (UF)')
                                    ->required(),
                            ]),
                        ]),
                ])
                ->columnSpanFull()
            ]);
        }
        
}