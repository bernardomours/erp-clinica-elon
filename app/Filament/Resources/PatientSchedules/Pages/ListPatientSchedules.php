<?php

namespace App\Filament\Resources\PatientSchedules\Pages;

use App\Filament\Resources\PatientSchedules\PatientScheduleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPatientSchedules extends ListRecords
{
    protected static string $resource = PatientScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
