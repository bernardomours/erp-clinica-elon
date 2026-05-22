<?php

namespace App\Filament\App\Widgets;

use App\Models\Expense;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ExpensesByCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Despesas por Categoria';
    
    protected static ?int $sort = 3; 
    
    protected ?string $pollingInterval = null;
    protected ?string $maxHeight = '300px';
    protected static bool $isDiscovered = false;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $tenantId = filament()->getTenant()?->id;
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();

        $despesas = Expense::with('category')
            ->when($tenantId, fn($q) => $q->where('clinic_id', $tenantId))
            ->whereBetween('due_date', [$inicioMes, $fimMes])
            ->get();

        $categorias = [];
        $valores = [];

        $agrupado = $despesas->groupBy(function($despesa) {
            return $despesa->category ? $despesa->category->name : 'Sem Categoria';
        });

        foreach ($agrupado as $nomeCategoria => $itens) {
            $valor = round($itens->sum('total_amount'), 2);
            
            $valorFormatado = number_format($valor, 2, ',', '.');
            
            // MÁGICA: Juntamos o nome com o valor formatado na mesma linha!
            $categorias[] = $nomeCategoria . " (R$ {$valorFormatado})"; 
            
            $valores[] = $valor;
        }

        $cores = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#6366f1', '#14b8a6'];

        return [
            'datasets' => [
                [
                    'label' => 'Total (R$)',
                    'data' => $valores,
                    'backgroundColor' => array_slice($cores, 0, count($valores)),
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => $categorias,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}