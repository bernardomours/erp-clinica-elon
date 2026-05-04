<?php

namespace App\Filament\Resources\PatientSchedules\Pages;

use App\Filament\Resources\PatientSchedules\PatientScheduleResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePatientSchedule extends CreateRecord
{
    protected static string $resource = PatientScheduleResource::class;

    protected function getRedirectUrl():string
    {
        return $this->getResource()::getUrl('index');
    }
}
