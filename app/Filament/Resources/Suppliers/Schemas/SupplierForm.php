<?php

namespace App\Filament\Resources\Suppliers\Schemas;

// Importações corretas do Filament Forms
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\Facades\Http;
use Filament\Support\RawJs;
use Filament\Schemas\Schema; 

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    
                    Step::make('Dados Pessoais')
                        ->label('Dados Pessoais')
                        ->description('Informações de identificação e contato do fornecedor.')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Select::make('clinic_id')
                                ->relationship('clinic', 'name')
                                ->label('Clínica')
                                ->visible(fn () => filament()->getCurrentPanel()->getId() === 'admin')
                                ->required(fn () => filament()->getCurrentPanel()->getId() === 'admin'),

                            Section::make('Dados do Fornecedor')
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Razão Social / Nome')
                                        ->placeholder('Ex: Dental Cremer')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('cpf_cnpj')
                                        ->label('CPF ou CNPJ')
                                        ->placeholder('00.000.000/0000-00')
                                        ->required()
                                        ->mask(RawJs::make(<<<'JS'
                                            $input.length > 14 ? '99.999.999/9999-99' : '999.999.999-99'
                                        JS))
                                        ->maxLength(18),

                                    TextInput::make('phone')
                                        ->label('Telefone / WhatsApp')
                                        ->tel()
                                        ->placeholder('(00) 00000-0000')
                                        ->required()
                                        ->maxLength(15),
                                    
                                    TextInput::make('email')
                                        ->label('Email')
                                        ->nullable()
                                        ->placeholder('example@example.com'),

                                    Toggle::make('is_active')
                                        ->label('Fornecedor Ativo')
                                        ->default(true)
                                        ->inline(false),
                                ])
                        ]),

                    Step::make('Endereco')
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
                        ])
                ])
                ->columnSpanFull()
            ]);
    }
}