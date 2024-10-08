<?php

namespace App\Filament\Resources\ProgramResource\Pages;

use App\Filament\Resources\ProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgram extends EditRecord
{
    protected static string $resource = ProgramResource::class;

    protected function getFormActions(): array
    {
        return array_merge(
            [
                \App\Filament\Actions\PageActions\ChangeStatus::make([
                    'related_model' => 'programServices',
                    'message' => 'Добавьте хотя бы одну услугу',
                    'status' => 'danger',
                ]),
            ],
            parent::getFormActions(),
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
