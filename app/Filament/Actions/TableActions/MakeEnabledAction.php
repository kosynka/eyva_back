<?php

namespace App\Filament\Actions\TableActions;

use Filament\Notifications\Notification;
use App\Enums\BuyableStatusEnum;
use Filament\Tables\Actions;
use Illuminate\Database\Eloquent;

class MakeEnabledAction
{
    public static function make(array $data = [])
    {
        return Actions\BulkAction::make('make_enabled')
            ->requiresConfirmation()
            ->color('success')
            ->label('Опубликовать отмеченное')
            ->icon('heroicon-o-eye-slash')
            ->action(function (Eloquent\Collection $selectedRecords) use ($data) {
                $selectedRecords->each(
                    function (Eloquent\Model $selectedRecord) use ($data) {
                        if (
                            isset($data['related_model']) &&
                            $selectedRecord->{$data['related_model']}()->count() < 1
                        ) {
                            $title = ($data['message'] ?? 'Добавьте хотя бы одну услугу/подарок')
                                . ' для ' . $selectedRecord->title;

                            Notification::make()
                                ->status($data['status'] ?? 'danger')
                                ->title($title)
                                ->send();
                        } else {
                            $selectedRecord->status = BuyableStatusEnum::ENABLED_KEY;
                            $selectedRecord->save();
                        }
                    }
                );
            });
    }
}
