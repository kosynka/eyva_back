<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('replenish_balance')
                ->color('success')
                ->label('Пополнить баланс')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\TextInput::make('amount')
                        ->label('Сумма')
                        ->default(1)
                        ->numeric()
                        ->minValue(1)
                        ->required(),
                    Forms\Components\TextInput::make('comment')
                        ->label('Комментарий')
                        ->minLength(1),
                ])
                ->action(function (array $data, User $record): void {
                    try {
                        DB::beginTransaction();
                        $result = true;
                        $this->userRepository->replenishBalance($record, $data);

                        DB::commit();
                    } catch (\Exception $exception) {
                        DB::rollBack();
                        $result = false;

                        Log::error($exception->getMessage(), $exception->getTrace());

                        Notification::make()
                            ->danger()
                            ->title('Проблема с пополнением баланса')
                            ->body($exception->getMessage())
                            ->send();
                    }

                    if ($result === true) {
                        Notification::make()
                            ->success()
                            ->title('Баланс успешно пополнен')
                            ->send();
                    }

                    redirect(static::getUrl(['record' => $record->id]));
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
