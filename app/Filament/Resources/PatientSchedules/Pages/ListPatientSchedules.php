<?php

namespace App\Filament\Resources\PatientSchedules\Pages;

use App\Filament\Resources\PatientSchedules\PatientScheduleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Actions\Action;

class ListPatientSchedules extends ListRecords
{
    protected static string $resource = PatientScheduleResource::class;

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos os Registos'),

            'hoje' => Tab::make('Hoje')
                ->icon('heroicon-m-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('schedule_date', Carbon::today())),

            'proximos_7' => Tab::make('Próximos 7 Dias')
                ->icon('heroicon-m-calendar')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('schedule_date', [
                    Carbon::today(),
                    Carbon::today()->addDays(7),
                ])),

            'concluidos' => Tab::make('Realizados')
                ->icon('heroicon-m-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Completed')),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Registrar Agendamento'),

            Action::make('emitir_recibo')
                ->label('Emitir Recibo (Via GOV)')
                ->icon('heroicon-o-document-arrow-up')
                ->color('success')
                ->url(fn () => 'https://sso.acesso.gov.br/login?client_id=www.gov.br&authorization_id=19e4b0e3ef5')
                #->url(fn ($record) => "https://portal.gov.br/emitir?cpf={$record->patient->cpf}&valor={$record->valor}") #esse aqui é uma opção para caso dê para predefinir o cpf do paciente por exemplo
                ->openUrlInNewTab(),
        ];
    }
}
