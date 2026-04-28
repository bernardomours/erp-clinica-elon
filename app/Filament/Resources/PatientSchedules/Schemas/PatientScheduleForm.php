<?php

namespace App\Filament\Resources\PatientSchedules\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

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
                DateTimePicker::make('schedule_date')
                    ->label('Data e Hora')
                    ->required()
                    ->displayFormat('d/m/Y H:i')
                    ->seconds(false),
                TextInput::make('procedure')
                    ->label('Anotações da Consulta')
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Status da Consulta')
                    ->options([
                        'Scheduled' => 'Agendada',
                        'Confirmed' => 'Confirmada',
                        'Completed' => 'Realizada',
                        'Cancelled' => 'Cancelada',
                    ])
                    ->default('Scheduled')
                    ->required(),
                Textarea::make('notes')
                    ->label('Notas Adicionais')
                    ->columnSpanFull(),
            ]);
    }
}
