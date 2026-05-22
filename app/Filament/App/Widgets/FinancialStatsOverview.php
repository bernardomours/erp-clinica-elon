<?php

namespace App\Filament\App\Widgets;

use App\Models\Revenue;
use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class FinancialStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected ?string $pollingInterval = null;
    protected int | string | array $columnSpan = 'full';
    protected static bool $isDiscovered = false;

    protected function getStats(): array
    {
        $tenantId = filament()->getTenant()?->id;
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();

        $faturamento = Revenue::when($tenantId, fn($q) => $q->where('clinic_id', $tenantId))
            ->whereBetween('created_at', [$inicioMes, $fimMes])
            ->where('status', 'paid')
            ->sum('total_amount');

        $receitasPendentes = Revenue::when($tenantId, fn($q) => $q->where('clinic_id', $tenantId))
            ->whereBetween('created_at', [$inicioMes, $fimMes])
            ->where('status', 'pending')
            ->sum('total_amount');

        $despesasPagas = Expense::when($tenantId, fn($q) => $q->where('clinic_id', $tenantId))
            ->whereBetween('due_date', [$inicioMes, $fimMes])
            ->where('status', 'paid')
            ->sum('total_amount');

        $contasPagar = Expense::when($tenantId, fn($q) => $q->where('clinic_id', $tenantId))
            ->whereBetween('due_date', [$inicioMes, $fimMes])
            ->where('status', 'pending')
            ->sum('total_amount');

        $lucroLiquido = $faturamento - $despesasPagas;

        return [
            Stat::make('Faturamento Realizado', 'R$ ' . number_format($faturamento, 2, ',', '.'))
                ->description('Total recebido no mês')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Despesas Pagas', 'R$ ' . number_format($despesasPagas, 2, ',', '.'))
                ->description('Total saído do caixa')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Lucro Líquido Real', 'R$ ' . number_format($lucroLiquido, 2, ',', '.'))
                ->description($lucroLiquido >= 0 ? 'Resultado positivo' : 'Prejuízo operacional')
                ->color($lucroLiquido >= 0 ? 'success' : 'danger'),

            Stat::make('Contas a Pagar', 'R$ ' . number_format($contasPagar, 2, ',', '.'))
                ->description('Boletos/gastos pendentes')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning'),

            Stat::make('Receitas à Receber', 'R$ ' . number_format($receitasPendentes, 2, ',', '.'))
                ->description('A receber de pacientes')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}