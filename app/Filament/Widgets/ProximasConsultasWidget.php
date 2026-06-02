<?php

namespace App\Filament\Widgets;

use App\Models\PatientSchedule;
use App\Models\Procedure;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Facades\Filament;
use Filament\Widgets\TableWidget as BaseWidget;

class ProximasConsultasWidget extends BaseWidget
{
    protected static ?string $heading = 'Próximos Lembretes de Consulta';
    protected ?string $pollingInterval = null;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PatientSchedule::query()
                    ->with(['customer']) 
                    ->when(Filament::getTenant(), function ($query, $tenant) {
                        return $query->where('clinic_id', $tenant->id);
                    })
                    ->where('schedule_date', '>=', now())
                    ->orderBy('schedule_date', 'asc')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('schedule_date')
                    ->label('Horário')
                    ->dateTime('d/m/Y')
                    ->description(fn ($record) => $record->schedule_date->format('H:i')),

                TextColumn::make('customer.name')
                    ->label('Paciente')
                    ->description(function ($record) {
                        if (!$record->procedure_id) {
                            return 'Sem procedimento';
                        }
                        
                        $procedimento = Procedure::withoutGlobalScopes()->find($record->procedure_id);
                        
                        return $procedimento ? $procedimento->name : 'Sem procedimento';
                    }),
                TextColumn::make('status')
                    ->label('Status') 
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Scheduled' => 'Agendado',
                        'Confirmed' => 'Confirmado',
                        'Completed' => 'Realizada',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Scheduled' => 'warning',
                        'Confirmed' => 'info',
                        'Completed' => 'success',
                        default => 'gray',
                    }),
            ]);
    }
}