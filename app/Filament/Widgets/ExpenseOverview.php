<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ExpenseOverview extends BaseWidget
{
    protected ?string $pollingInterval = null;
    
    protected function getStats(): array
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();

        $totalMes = Expense::whereBetween('due_date', [$inicioMes, $fimMes])->sum('total_amount');
        $pago = Expense::whereBetween('due_date', [$inicioMes, $fimMes])->where('status', 'paid')->sum('total_amount');
        $pendente = Expense::whereBetween('due_date', [$inicioMes, $fimMes])->where('status', 'pending')->sum('total_amount');

        return [
            Stat::make('Total de Despesas (Mês)', 'R$ ' . number_format($totalMes, 2, ',', '.'))
                ->description('Total empenhado no mês')
                ->color('gray'),
                
            Stat::make('Contas Pagas', 'R$ ' . number_format($pago, 2, ',', '.'))
                ->description('O que já saiu do caixa')
                ->icon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Contas a Pagar', 'R$ ' . number_format($pendente, 2, ',', '.'))
                ->description('O que ainda vence este mês')
                ->icon('heroicon-m-clock')
                ->color('danger'),
        ];
    }
}