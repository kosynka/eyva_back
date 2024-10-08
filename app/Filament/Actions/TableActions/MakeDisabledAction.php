<?php

namespace App\Filament\Actions\TableActions;

use App\Enums\BuyableStatusEnum;
use Filament\Tables\Actions;
use Illuminate\Database\Eloquent;

class MakeDisabledAction
{
    public static function make()
    {
        return Actions\BulkAction::make('make_disabled')
            ->requiresConfirmation()
            ->color('warning')
            ->label('Скрыть отмеченное')
            ->icon('heroicon-o-eye-slash')
            ->action(function (Eloquent\Collection $selectedRecords) {
                $selectedRecords->each(
                    function (Eloquent\Model $selectedRecord) {
                        $selectedRecord->status = BuyableStatusEnum::DISABLED_KEY;
                        $selectedRecord->save();
                    }
                );
            });
    }
}
