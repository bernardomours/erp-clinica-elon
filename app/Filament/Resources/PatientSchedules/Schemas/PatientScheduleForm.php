<?php

namespace App\Filament\Resources\PatientSchedules\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use App\Models\Procedure;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PatientSchedule;

class PatientScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('clinic_id')
                    ->relationship('clinic', 'name')
                    ->label('Clínica')
                    ->visible(fn () => filament()->getCurrentPanel()->getId() === 'admin')
                    ->required(fn () => filament()->getCurrentPanel()->getId() === 'admin'),

                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->label('Paciente')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('procedure_id')
                    ->label('Procedimento')
                    ->options(function () {
                        $tenant = filament()->getTenant();
                        if ($tenant) {
                            return Procedure::where('clinic_id', $tenant->id)->pluck('name', 'id');
                        }
                        return Procedure::pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->placeholder('Selecione um procedimento')
                    ->columnSpanFull(),

                DateTimePicker::make('schedule_date')
                    ->label('Data e Hora')
                    ->required()
                    ->displayFormat('d/m/Y H:i')
                    ->seconds(false),

                Select::make('status')
                    ->label('Status')
                    ->required()
                    ->options(function (?\App\Models\PatientSchedule $record) {
                        $opcoes = [
                            'Scheduled' => 'Agendado',
                            'Confirmed' => 'Confirmado',
                        ];
                        if ($record && $record->status === 'Completed') {
                            $opcoes['Completed'] = 'Concluído / Faturado';
                        }

                        return $opcoes;
                    })
                    ->disabled(fn (?PatientSchedule $record) => $record && $record->status === 'Completed')
                    ->default('Scheduled'),

                Textarea::make('notes')
                    ->label('Notas Adicionais')
                    ->placeholder('Observações importantes sobre o atendimento...')
                    ->columnSpanFull(),
            ]);
    }
}