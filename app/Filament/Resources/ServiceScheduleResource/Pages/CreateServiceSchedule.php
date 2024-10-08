<?php

namespace App\Filament\Resources\ServiceScheduleResource\Pages;

use App\Filament\Resources\ServiceScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceSchedule extends CreateRecord
{
    protected static string $resource = ServiceScheduleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['places_count_left'] = $data['places_count_total'];

        return $data;
    }
}
