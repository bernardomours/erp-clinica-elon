<?php

namespace App\Filament\App\Widgets;

use App\Models\Revenue;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenuesByCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Receitas por Procedimento';
    
    protected static ?int $sort = 3; 
    protected ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = 1;
    protected ?string $pollingInterval = null;
    protected static bool $isDiscovered = false;

    protected function getData(): array
    {
        $tenantId = filament()->getTenant()?->id;
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();

        $lucros = Revenue::when($tenantId, fn($q) => $q->where('clinic_id', $tenantId))
            ->whereBetween('created_at', [$inicioMes, $fimMes])
            ->where('status', 'paid')
            ->get();

        $categorias = [];
        $valores = [];

        $agrupado = $lucros->groupBy('description');

        foreach ($agrupado as $procedimento => $itens) {
            $nomeExibicao = str_replace('Procedimento: ', '', $procedimento);
            $nomeBase = $nomeExibicao ?: 'Outros';
            $valor = round($itens->sum('total_amount'), 2);  
            $valorFormatado = number_format($valor, 2, ',', '.');
            $categorias[] = $nomeBase . " (R$ {$valorFormatado})";
            $valores[] = $valor;
        }

        $cores = ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ec4899', '#14b8a6'];

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