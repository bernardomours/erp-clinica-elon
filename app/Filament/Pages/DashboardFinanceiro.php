<?php

namespace App\Filament\Pages;

use App\Filament\App\Widgets\FinancialChart;
use App\Filament\App\Widgets\FinancialStatsOverview;
use App\Filament\App\Widgets\ExpensesByCategoryChart;
use App\Filament\App\Widgets\RevenuesByCategoryChart;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;

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
}