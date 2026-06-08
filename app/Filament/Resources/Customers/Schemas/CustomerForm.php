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
use Filament\Facades\Filament;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Carbon;
use App\Rules\CpfCnpjRule;

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
                                    ->visible(fn () => Filament::getCurrentPanel()->getId() === 'admin')
                                    ->required(fn () => Filament::getCurrentPanel()->getId() === 'admin'),
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
                                    ->rule(new CpfCnpjRule())
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'Esse CPF ou CNPJ já está cadastrado.'
                                    ]),
                                    
                                DatePicker::make('birth_date')
                                    ->label('Data de Nascimento')
                                    ->displayFormat('d/m/Y')
                                    ->live(debounce:500),
                                    
                                TextInput::make('phone')
                                    ->label('Telefone/WhatsApp')
                                    ->required() 
                                    ->visible(function (Get $get) {
                                        $birthDate = $get('birth_date');
                                        if (!$birthDate) return true;
                                        return Carbon::parse($birthDate)->age >= 18;
                                    }),

                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->visible(function (Get $get) {
                                        $birthDate = $get('birth_date');
                                        if (!$birthDate) return true;
                                        
                                        return Carbon::parse($birthDate)->age >= 18;
                                    }),
                            ]),
                            
                            Toggle::make('is_active')
                                ->label('Cadastro Ativo?')
                                ->default(true)
                                ->inline(false),
                        ]),
                    Step::make('Responsável Legal')
                        ->icon('heroicon-o-users')
                        ->description('Obrigatório para menores de 18 anos')
                        ->visible(function (Get $get) {
                            $birthDate = $get('birth_date');
                            if (!$birthDate) return false;
                            
                            return Carbon::parse($birthDate)->age < 18;
                        })
                        ->schema([
                            Select::make('responsible_id')
                                ->label('Selecione ou Cadastre o Responsável')
                                ->relationship('responsible', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->label('Nome do Responsável')
                                        ->required(),
                                    TextInput::make('cpf')
                                        ->label('CPF do Responsável')
                                        ->mask('999.999.999-99')
                                        ->required(),
                                    TextInput::make('phone')
                                        ->label('Telefone/WhatsApp')
                                        ->mask('(99) 99999-9999')
                                        ->required(),
                                    TextInput::make('email')
                                        ->label('Email')
                                        ->nullable(),
                                ]),
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
                                    ->dehydrateStateUsing(fn ($state) => str_replace('-', '', $state)) 
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($set, ?string $state) {
                                        if (blank($state)) return;

                                        $cep = preg_replace('/[^0-9]/', '', $state);
                                        if (strlen($cep) !== 8) return;

                                        $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");

                                        if ($response->successful() && !isset($response['erro'])) {
                                            $data = $response->json();
                                            
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