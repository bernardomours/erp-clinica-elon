<?php

namespace App\Filament\Pages;

use App\Filament\App\Widgets\FinancialChart;
use App\Filament\App\Widgets\FinancialStatsOverview;
use App\Filament\App\Widgets\ExpensesByCategoryChart;
use App\Filament\App\Widgets\RevenuesByCategoryChart;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Infolists\Components\TextEntry;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;

class DashboardFinanceiro extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    
    //protected string $view = 'filament.app.pages.dashboard-financeiro';   

    protected static ?int $navigationSort = 1;
    
    protected static ?string $navigationLabel = 'Dashboard'; 
    
    protected static ?string $title = 'Dashboard Financeiro';
    
    protected static string $routePath = 'dashboard-financeiro';

    public function getWidgets(): array
    {
        return [
            FinancialStatsOverview::class,
            FinancialChart::class,
            ExpensesByCategoryChart::class,
            RevenuesByCategoryChart::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        $tenant = filament()->getTenant();

        // Segurança: Se estiver no painel admin global e não tiver uma clínica selecionada, não exibe os botões
        if (!$tenant) {
            return [];
        }

        return [
            Action::make('ver_receitas')
                ->label('Ver Receitas (Mês)')
                ->icon('heroicon-o-arrow-trending-up')
                ->color('success')
                ->record($tenant)
                ->modalHeading('Receitas do Mês Atual')
                ->modalDescription('Lista detalhada de todas as entradas financeiras deste mês.')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Fechar')
                ->infolist([
                    RepeatableEntry::make('currentMonthRevenues')
                        ->label('')
                        ->schema([
                            TextEntry::make('description')
                                ->label('Descrição')
                                ->weight('bold'),
                                
                            TextEntry::make('total_amount')
                                ->label('Valor')
                                ->money('BRL')
                                ->badge()
                                ->color('success'),
                                
                            TextEntry::make('created_at')
                                ->label('Data')
                                ->date('d/m/Y'),
                                
                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    'received' => 'Recebido',
                                    'paid' => 'Recebido',
                                    'pending' => 'Pendente',
                                    default => $state,
                                })
                                ->color(fn (string $state): string => match ($state) {
                                    'received' || 'paid' => 'success',
                                    'pending' => 'warning',
                                    default => 'gray',
                                }),
                        ])->columns(4)
                ]),

            Action::make('ver_despesas')
                ->label('Ver Despesas (Mês)')
                ->icon('heroicon-o-arrow-trending-down')
                ->color('danger')
                ->record($tenant) // Define a clínica atual como o registro de onde puxar os dados
                ->modalHeading('Despesas do Mês Atual')
                ->modalDescription('Lista detalhada de todas as saídas.')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Fechar')
                ->infolist([
                    RepeatableEntry::make('currentMonthExpenses') // <-- Requer a função criada no model Clinic
                        ->label('')
                        ->schema([
                            TextEntry::make('description')
                                ->label('Descrição')
                                ->weight('bold'),
                                
                            TextEntry::make('total_amount')
                                ->label('Valor')
                                ->money('BRL')
                                ->badge()
                                ->color('danger'),
                                
                            TextEntry::make('due_date')
                                ->label('Vencimento')
                                ->date('d/m/Y'),
                                
                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    'paid' => 'Pago',
                                    'pending' => 'Pendente',
                                    default => $state,
                                })
                                ->color(fn (string $state): string => match ($state) {
                                    'paid' => 'success',
                                    'pending' => 'warning',
                                    default => 'gray',
                                }),
                        ])->columns(4)
                ]),
                
            // Você pode duplicar a Action acima para 'ver_receitas' e usar a cor 'success' e o ícone 'heroicon-o-arrow-trending-up'
        ];
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