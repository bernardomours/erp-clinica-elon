<?php

namespace App\Providers\Filament;

use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\PatientSchedules\PatientScheduleResource;
use App\Filament\Resources\Revenues\RevenueResource;
use App\Filament\Resources\FinancialCategories\FinancialCategoryResource;
use App\Filament\Resources\Patients\Pages\PatientSchedule;
use App\Models\Clinic;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\Navigation\MenuItem;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pages\ExpensesDashboard;
use App\Filament\Resources\Procedures\ProcedureResource;
use App\Filament\Resources\ProductPurchases\ProductPurchaseResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Pages\DashboardFinanceiro;
use App\Filament\Resources\Suppliers\SupplierResource;
use App\Filament\Widgets\ProximasConsultasWidget;
use App\Filament\Pages\Auth\CustomLogin;
use App\Filament\Widgets\MonthlyBirthdays;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            //->spa()
            ->id('app')
            ->path('app')
            ->login()
            ->colors([
                'primary' => Color::Sky,
            ])
            ->brandLogo(url('/images/topbar_icon.png'))
            ->brandLogoHeight('8rem')
            ->brandName('OdontoFlow')
            ->favicon(url('/images/favicon.png'))
            ->resources([
                CustomerResource::class,
                PatientScheduleResource::class,
                RevenueResource::class,
                FinancialCategoryResource::class,
                ProcedureResource::class,
                ProductPurchaseResource::class,
                ProductResource::class,
                RevenueResource::class,
                SupplierResource::class,
            ])

            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                Dashboard::class,
                ExpensesDashboard::class,
                DashboardFinanceiro::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->navigationGroups([
                'Frequência',
                'Financeiro',
                'Estoque',
            ])
            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
                ProximasConsultasWidget::class,
                MonthlyBirthdays::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            
            ->tenant(Clinic::class, slugAttribute: 'slug') 
            ->tenantRoutePrefix('consultorio')
            ->tenantMenuItems([
                MenuItem::make()
                    ->label('Ver minha equipe')
                    ->icon('heroicon-m-users')
                    ->url("#ver-equipe"),
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn (): string => \Illuminate\Support\Facades\Blade::render('<livewire:equipe-modal />'),
            );
        }
}