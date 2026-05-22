<?php

namespace App\Filament\App\Widgets;

use App\Models\Revenue;
use App\Models\Expense;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Filament\Support\RawJs;

class FinancialChart extends ChartWidget
{
    protected ?string $heading = 'Análise Financeira';
    
    protected ?string $maxHeight = '300px'; 
    protected int | string | array $columnSpan = 2;
    protected ?string $pollingInterval = null;
    protected static bool $isDiscovered = false;
    

    protected function getFilters(): ?array
    {
        return [
            '1' => 'Este Mês',
            '3' => 'Últimos 3 Meses',
            '6' => 'Últimos 6 Meses',
            '12' => 'Últimos 12 Meses',
        ];
    }

    protected function getData(): array
    {
        $mesesFiltro = $this->filter ? (int) $this->filter : 1;

        $meses = [];
        $receitas = [];
        $despesas = [];

        for ($i = $mesesFiltro - 1; $i >= 0; $i--) {
            $mesAtual = Carbon::now()->subMonths($i);
            
            $meses[] = $mesAtual->translatedFormat('M/Y');

            $tenantId = filament()->getTenant()?->id;

            $somaReceitas = Revenue::when($tenantId, fn($query) => $query->where('clinic_id', $tenantId))
                ->whereMonth('created_at', $mesAtual->month)
                ->whereYear('created_at', $mesAtual->year)
                ->where('status', 'paid')
                ->sum('total_amount');

            $somaDespesas = 0;
            if (class_exists(Expense::class)) {
                $somaDespesas = Expense::when($tenantId, fn($query) => $query->where('clinic_id', $tenantId))
                    ->whereMonth('due_date', $mesAtual->month)
                    ->whereYear('due_date', $mesAtual->year)
                    ->sum('total_amount');
            }

            $receitas[] = round($somaReceitas, 2);
            $despesas[] = round($somaDespesas, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Receitas (R$)',
                    'data' => $receitas,
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#10b981',
                ],
                [
                    'label' => 'Despesas (R$)',
                    'data' => $despesas,
                    'backgroundColor' => '#ef4444',
                    'borderColor' => '#ef4444',
                ],
            ],
            'labels' => $meses,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'layout' => [
                'padding' => [
                    'top' => 35, // Cria um teto para o número não ser cortado
                ],
            ],
            'animation' => [
                // Assim que a animação das barras terminar, desenhamos os números!
                'onComplete' => RawJs::make('function(context) {
                    var chart = context.chart;
                    var ctx = chart.ctx;
                    
                    ctx.font = "bold 12px sans-serif";
                    ctx.textAlign = "center";
                    ctx.textBaseline = "bottom";
                    
                    // MÁGICA: Lê o tema atual. Escuro = Cinza Claro / Claro = Cinza Escuro
                    ctx.fillStyle = document.documentElement.classList.contains("dark") ? "#e4e4e7" : "#3f3f46";

                    chart.data.datasets.forEach(function(dataset, i) {
                        var meta = chart.getDatasetMeta(i);
                        meta.data.forEach(function(bar, index) {
                            var data = dataset.data[index];
                            
                            // Só escrevemos o número se for maior que zero
                            if (data > 0) { 
                                var valor = parseFloat(data).toLocaleString("pt-BR", {minimumFractionDigits: 2});
                                ctx.fillText(valor, bar.x, bar.y - 6);
                            }
                        });
                    });
                }'),
            ],
        ];
    }
}