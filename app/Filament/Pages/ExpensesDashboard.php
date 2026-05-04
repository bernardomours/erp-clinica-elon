<?php

namespace App\Filament\Pages;

use App\Models\Expense;
use App\Filament\Widgets\ExpenseOverview;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Carbon;
use BackedEnum;
use UnitEnum;

class ExpensesDashboard extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationLabel = 'Painel de Despesas';
    protected static ?string $title = 'Gestão Financeira - Despesas';
    protected static string|UnitEnum|null $navigationGroup = 'Financeiro';
    protected string $view = 'filament.pages.expenses-dashboard';

    // Registar os cards no topo da página
    protected function getHeaderWidgets(): array
    {
        return [
            ExpenseOverview::class,
        ];
    }

    // Botão "Lançar Despesa" que abre um Modal
    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_expense')
                ->label('Lançar Nova Despesa')
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->form([
                    Forms\Components\TextInput::make('description')
                        ->label('Descrição da Conta')
                        ->required(),
                        
                    // CORREÇÃO: Usar options() com a query direta
                    Forms\Components\Select::make('financial_category_id')
                        ->label('Categoria')
                        ->options(fn () => \App\Models\FinancialCategory::query()
                            ->where('type', 'expense')
                            ->where('clinic_id', filament()->getTenant()?->id)
                            ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\DatePicker::make('due_date')
                        ->label('Vencimento')
                        ->required(),

                    Forms\Components\TextInput::make('total_amount')
                        ->label('Valor Total')
                        ->numeric()
                        ->prefix('R$')
                        ->required(),

                    // Adicionado para satisfazer a sua tabela de Expense
                    Forms\Components\Select::make('payment_plan')
                        ->label('Forma de Pagamento')
                        ->options([
                            'Boleto' => 'Boleto Bancário',
                            'PIX' => 'PIX',
                            'Transferência' => 'Transferência',
                            'Cartão' => 'Cartão',
                            'Dinheiro' => 'Dinheiro',
                        ])
                        ->default('Boleto')
                        ->required(),

                    Forms\Components\TextInput::make('installments')
                        ->label('Qtd. Parcelas')
                        ->numeric()
                        ->default(1)
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->label('Situação Inicial')
                        ->options([
                            'pending' => 'Pendente', 
                            'paid' => 'Pago'
                        ])
                        ->default('pending')
                        ->required(),
                ])
                ->action(function (array $data) {
                    // Preenche a clínica
                    $data['clinic_id'] = filament()->getTenant()->id;
                    
                    // Calcula o valor da parcela automaticamente para o banco não reclamar
                    $parcelas = intval($data['installments']);
                    $data['installment_amount'] = $parcelas > 0 
                        ? round($data['total_amount'] / $parcelas, 2) 
                        : $data['total_amount'];

                    // Salva a despesa
                    Expense::create($data);
                })
                ->successNotificationTitle('Despesa lançada com sucesso!'),
        ];
    }

    // Configuração da Tabela que aparece abaixo dos cards
    public function table(Table $table): Table
    {
        return $table
            ->query(Expense::query()->whereBetween('due_date', [now()->startOfMonth(), now()->endOfMonth()]))
            ->columns([
                Tables\Columns\TextColumn::make('due_date')->label('Vencimento')->date('d/m'),
                Tables\Columns\TextColumn::make('description')->label('Descrição'),
                Tables\Columns\TextColumn::make('category.name')->label('Categoria'),
                Tables\Columns\TextColumn::make('total_amount')->label('Valor')->money('BRL'),
                Tables\Columns\SelectColumn::make('status')
                    ->label('Situação')
                    ->options(['pending' => 'Pendente', 'paid' => 'Pago']),
            ])
            ->actions([
             //DeleteAction::make(),
            ]);
    }
}