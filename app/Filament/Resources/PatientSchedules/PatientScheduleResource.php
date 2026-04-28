<?php

namespace App\Filament\Resources\PatientSchedules;

use App\Filament\Resources\PatientSchedules\Pages\CreatePatientSchedule;
use App\Filament\Resources\PatientSchedules\Pages\EditPatientSchedule;
use App\Filament\Resources\PatientSchedules\Pages\ListPatientSchedules;
use App\Filament\Resources\PatientSchedules\Schemas\PatientScheduleForm;
use App\Filament\Resources\PatientSchedules\Tables\PatientSchedulesTable;
use App\Models\PatientSchedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PatientScheduleResource extends Resource
{
    protected static ?string $model = PatientSchedule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

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

    public static function getPages(): array
    {
        return [
            'index' => ListPatientSchedules::route('/'),
            'create' => CreatePatientSchedule::route('/create'),
            'edit' => EditPatientSchedule::route('/{record}/edit'),
        ];
    }
}
