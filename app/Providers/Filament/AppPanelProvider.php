<?php

namespace App\Providers\Filament;

use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\PatientSchedules\PatientScheduleResource;
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

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->spa()
            ->id('app')
            ->path('app')
            ->login()
            ->colors([
                'primary' => Color::Sky,
            ])
            ->resources([
                CustomerResource::class,
                PatientScheduleResource::class,
            ])

            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
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
            
            // 3. A MÁGICA DA BLINDAGEM (Tenancy)
            ->tenant(Clinic::class, slugAttribute: 'slug') 
            ->tenantRoutePrefix('consultorio')
            ->tenantMenuItems([
                MenuItem::make()
                    ->label('Ver minha equipe')
                    ->icon('heroicon-m-users')
                    // O truque: Executar o JS direto no atributo href (url) do link
                    ->url("#ver-equipe"),
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn (): string => \Illuminate\Support\Facades\Blade::render('<livewire:equipe-modal />'),
            );
        }
}