<?php

namespace App\Filament\Widgets;

use App\Models\PatientSchedule;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class ProximasConsultasWidget extends BaseWidget
{
    protected static ?string $heading = 'Próximos Lembretes de Consulta';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PatientSchedule::query()
                    ->when(filament()->getTenant(), function ($query, $tenant) {
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
                    ->label('Paciente'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Scheduled' => 'warning',
                        'Confirmed' => 'info',
                        'Completed' => 'success',
                        default => 'gray',
                    }),
            ]);
    }
}