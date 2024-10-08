<?php

namespace App\Filament\Actions\ComponentActions;

use App\Enums\BuyableStatusEnum;
use Filament\Forms\Components\Actions;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent;

class ChangeStatus
{
    public static function make(array $data = [])
    {
        return Actions\Action::make('change_status_component')
            ->color(function (?Eloquent\Model $record) {
                if ($record?->status === BuyableStatusEnum::DISABLED_KEY) {
                    return 'success';
                } else if ($record?->status === BuyableStatusEnum::ENABLED_KEY) {
                    return 'warning';
                } else {
                    return 'danger';
                }
            })
            ->label(function (?Eloquent\Model $record) {
                if ($record?->status === BuyableStatusEnum::DISABLED_KEY) {
                    return 'Опубликовать';
                } else if ($record?->status === BuyableStatusEnum::ENABLED_KEY) {
                    return 'Скрыть';
                } else {
                    return 'Произошла ошибка';
                }
            })
            ->action(function (?Eloquent\Model $record) use ($data) {
                $status = 'success';

                if ($record?->status === BuyableStatusEnum::DISABLED_KEY) {
                    if (isset($data['related_model']) && $record?->{$data['related_model']}()->count() < 1) {
                        $text = $data['message'] ?? 'Добавьте хотя бы одну услугу/подарок';
                        $status = $data['status'] ?? 'danger';
                    } else {
                        $record->status = BuyableStatusEnum::ENABLED_KEY;
                        $text = 'Опубликована';
                    }
                } else if ($record?->status === BuyableStatusEnum::ENABLED_KEY) {
                    $record->status = BuyableStatusEnum::DISABLED_KEY;
                    $text = 'Скрыта';
                }

                Notification::make()
                    ->status($status)
                    ->title($text)
                    ->send();

                if ($record) {
                    $record->save();
                }
            })
            ->size('xl');
    }
}
