<?php

namespace App\Filament\Resources\PatientSchedules;

use App\Filament\Resources\PatientSchedules\Pages\CreatePatientSchedule;
use App\Filament\Resources\PatientSchedules\Pages\EditPatientSchedule;
use App\Filament\Resources\PatientSchedules\Pages\ListPatientSchedules;
use App\Filament\Resources\PatientSchedules\Schemas\PatientScheduleForm;
use App\Filament\Resources\PatientSchedules\Tables\PatientSchedulesTable;
use App\Models\PatientSchedule;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Table;

class PatientScheduleResource extends Resource
{
    protected static ?string $model = PatientSchedule::class;

    protected static ?string $modelLabel = 'Agendamento';
    protected static ?string $pluralModelLabel = 'Agendamentos  ';
    protected static ?string $navigationLabel = 'Agendamentos';
    protected static string|UnitEnum|null $navigationGroup = 'Frequência';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    // protected static ?string $recordTitleAttribute = 'id';
    public static function getRecordTitle(?Model $record): string
    {
        return $record ? "Consulta de {$record->customer->name}" : 'Agendamento';
    }

    public static function form(Schema $schema): Schema
    {
        return PatientScheduleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PatientSchedulesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        if (filament()->getCurrentPanel()->getId() === 'admin') {
            return true;
        }

        return (bool) filament()->getTenant()?->has_scheduling;
    }

    public static function canViewAny(): bool
    {
        if (filament()->getCurrentPanel()->getId() === 'admin') {
            return true;
        }
        
        return (bool) filament()->getTenant()?->has_scheduling;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPatientSchedules::route('/'),
            'create' => CreatePatientSchedule::route('/create'),
            'edit' => EditPatientSchedule::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('schedule_date', today())
            ->where('status', 'Scheduled')
            ->count() ?: null;
    }
}
