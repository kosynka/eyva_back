<?php

namespace App\Filament\Resources\ServiceScheduleResource\Pages;

use App\Filament\Resources\ServiceScheduleResource;
use App\Models\Service;
use App\Models\ServiceSchedule;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ListServiceSchedules extends ListRecords
{
    protected static string $resource = ServiceScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('copy_schedule')
                ->label('Копировать расписание')
                ->steps([
                    Forms\Components\Wizard\Step::make('Копировать расписание всех услуг(либо выбранной услуги) с даты по дату')
                        ->schema([
                            Forms\Components\DatePicker::make('from_date')
                                ->label('Копировать с даты')
                                ->required(),
                            Forms\Components\DatePicker::make('to_date')
                                ->label('Копировать по дату')
                                ->required(),
                            Forms\Components\Select::make('service_id')
                                ->label('Услуга')
                                ->options(Service::all()->pluck('title', 'id')->toArray())
                                ->nullable()
                                ->searchable(),
                        ]),
                    Forms\Components\Wizard\Step::make('Выберите дату начала для нового расписания')
                        ->schema([
                            Forms\Components\DatePicker::make('new_from_date')
                                ->label('Новая дата начала')
                                ->required(),
                        ]),
                ])
                ->action(function (array $data) {
                    DB::transaction(function () use ($data) {
                        $schedulesToCopy = ServiceSchedule::query()
                            ->whereBetween('start_date', [$data['from_date'], $data['to_date']])
                            ->when($data['service_id'], function(Builder $query) use ($data) {
                                $query->where('service_id', $data['service_id']);
                            })
                            ->get();

                        $selectedRecords = $schedulesToCopy->sortBy('start_date');
                        $firstRecord = $selectedRecords->first();
                        $diff = \Carbon\Carbon::parse($data['from_date'])
                            ->diffInDays($firstRecord->start_date);

                        foreach ($schedulesToCopy as $schedule) {
                            ServiceSchedule::create([
                                'service_id' => $schedule->service_id,
                                'start_date' => $data['new_from_date'],
                                'start_time' => $schedule->start_time,
                                'hall' => $schedule->hall,
                                'places_count_left' => $schedule->places_count_left,
                                'complexity' => $schedule->complexity,
                            ]);
                        }
                    });

                    Notification::make()
                        ->title('Копирование успешно завершено')
                        ->success()
                        ->send();
                }),
        ];
    }
}
