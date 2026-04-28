<?php

namespace App\Filament\Resources\PatientSchedules\Pages;

use App\Filament\Resources\PatientSchedules\PatientScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPatientSchedule extends EditRecord
{
    protected static string $resource = PatientScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
