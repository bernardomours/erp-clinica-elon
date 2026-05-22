<?php

namespace App\Filament\Pages;

use App\Models\Expense;
use App\Filament\Widgets\ExpenseOverview;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Carbon;
use App\Models\FinancialCategory;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use BackedEnum;
use UnitEnum;

class ExpensesDashboard extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationLabel = 'Painel de Despesas';
    protected static ?string $title = 'Gestão Financeira - Despesas';
    protected static string|UnitEnum|null $navigationGroup = 'Financeiro';
    protected static ?int $navigationSort = 2;
    protected string $view = 'filament.pages.expenses-dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            ExpenseOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_expense')
                ->label('Lançar Nova Despesa')
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->form([
                    TextInput::make('description')
                        ->label('Descrição da Conta')
                        ->required(),
                        
                    Select::make('financial_category_id')
                        ->label('Categoria')
                        ->options(fn () => FinancialCategory::query()
                            ->where('type', 'expense')
                            ->where('clinic_id', filament()->getTenant()?->id)
                            ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    DatePicker::make('due_date')
                        ->label('Vencimento')
                        ->required(),

                    TextInput::make('total_amount')
                        ->label('Valor Total')
                        ->numeric()
                        ->prefix('R$')
                        ->required(),

                    Select::make('payment_plan')
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

                    TextInput::make('installments')
                        ->label('Qtd. Parcelas')
                        ->numeric()
                        ->default(1)
                        ->required(),

                    Select::make('status')
                        ->label('Situação Inicial')
                        ->options([
                            'pending' => 'Pendente', 
                            'paid' => 'Pago'
                        ])
                        ->default('pending')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $data['clinic_id'] = filament()->getTenant()->id;
                    
                    $parcelas = intval($data['installments']);
                    $data['installment_amount'] = $parcelas > 0 
                        ? round($data['total_amount'] / $parcelas, 2) 
                        : $data['total_amount'];

                    Expense::create($data);
                })
                ->successNotificationTitle('Despesa lançada com sucesso!'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                    Expense::query()
                        ->when(filament()->getTenant(), fn($query, $tenant) => $query->where('clinic_id', $tenant->id))
                        ->whereBetween('due_date', [now()->startOfMonth(), now()->endOfMonth()])
                )
            ->columns([
                TextColumn::make('due_date')
                    ->label('Vencimento')
                    ->date('d/m'),
                TextColumn::make('description')
                    ->label('Descrição'),
                TextColumn::make('category.name')
                    ->label('Categoria'),
                TextColumn::make('total_amount')
                    ->label('Valor')
                    ->money('BRL'),
                SelectColumn::make('status')
                    ->label('Situação')
                    ->options(['pending' => 'Pendente', 'paid' => 'Pago']),
            ])->defaultSort('due_date', 'desc')
            ->actions([
                //EditAction::make(),
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        if (filament()->getCurrentPanel()->getId() === 'admin') {
            return true;
        }

        return (bool) filament()->getTenant()?->has_financial;
    }

    public static function canViewAny(): bool
    {
        if (filament()->getCurrentPanel()->getId() === 'admin') {
            return true;
        }
        
        return (bool) filament()->getTenant()?->has_financial;
    }
}