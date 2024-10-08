<?php

namespace App\Filament\Resources\AbonnementResource\Pages;

use App\Filament\Resources\AbonnementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbonnement extends EditRecord
{
    protected static string $resource = AbonnementResource::class;

    protected function getFormActions(): array
    {
        return array_merge(
            [
                \App\Filament\Actions\PageActions\ChangeStatus::make(),
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
