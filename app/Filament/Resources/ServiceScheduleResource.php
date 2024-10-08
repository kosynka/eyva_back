<?php

namespace App\Filament\Resources;

use App\Enums\ComplexityEnum;
use App\Filament\Resources\ServiceScheduleResource\Pages;
use App\Filament\Resources\ServiceScheduleResource\RelationManagers;
use App\Models\Service;
use App\Models\ServiceSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent;
use Illuminate\Support\Facades\DB;

class ServiceScheduleResource extends Resource
{
    protected static ?string $model = ServiceSchedule::class;
    protected static ?string $navigationLabel = 'Расписание';
    protected static ?string $modelLabel = 'Расписания';
    protected static ?string $pluralModelLabel = 'Расписание';
    protected static ?string $navigationIcon = 'heroicon-c-calendar-date-range';
    protected static ?string $activeNavigationIcon = 'heroicon-c-calendar-date-range';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('service_id')
                    ->label('Услуга')
                    ->relationship('service', 'title')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $service = Service::find((int) $state);

                        if (isset($service)) {
                            $set('complexity', $service->complexity);
                        }
                    })
                    ->required(),
                Forms\Components\Select::make('hall')
                    ->label('Зал')
                    ->options(ServiceSchedule::getHalls()),
                Forms\Components\DatePicker::make('start_date')
                    ->label('Дата')
                    ->required(),
                Forms\Components\TimePicker::make('start_time')
                    ->label('Время')
                    ->required(),
                Forms\Components\TextInput::make('places_count_total')
                    ->label('Мест было изначально')
                    ->numeric()
                    ->minValue(0)
                    ->reactive()
                    ->afterStateUpdated(function ($context, $state, Forms\Set $set) {
                        if ($context === 'create') {
                            $set('places_count_left', $state);
                        }
                    })
                    ->required(),
                Forms\Components\TextInput::make('places_count_left')
                    ->label('Мест осталось')
                    ->numeric()
                    ->minValue(0)
                    ->reactive()
                    ->afterStateUpdated(function ($record, $state, Forms\Set $set, Forms\Get $get) {
                        $diff = $state - $record->places_count_left;
                        $set('places_count_total', $record->places_count_total + $diff);
                    })
                    ->disabled(fn($context) => $context === 'create')
                    ->required(fn($context) => $context === 'edit'),
                Forms\Components\Select::make('complexity')
                    ->label('Сложность')
                    ->options(ComplexityEnum::getAllWithText()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('service.title')
                    ->label('Услуга')
                    ->getStateUsing(
                        fn (ServiceSchedule $record) => "{$record->service->title} ({$record->service->getType(true)})"
                    )
                    ->sortable()
                    ->searchable()
                    ->alignRight(),
                Tables\Columns\TextColumn::make('hall')
                    ->label('Зал')
                    ->getStateUsing(fn (ServiceSchedule $record): string => $record->getHall())
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Дата')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Время')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('places_count_left')
                    ->label('Осталось мест')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('places_count_total')
                    ->label('Всего мест')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('complexity')
                    ->label('Сложность')
                    ->formatStateUsing(fn($state) => ComplexityEnum::getOneWithText($state))
                    ->sortable()
                    ->searchable(),
            ])
            ->defaultSort('start_date', 'desc')
            ->defaultSort('start_time', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('service_id')
                    ->label('Услуга')
                    ->relationship('service', 'title')
                    ->options(Service::all()->pluck('title', 'id'))
                    ->preload()
                    ->multiple()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('hall')
                    ->label('Зал')
                    ->attribute('hall')
                    ->options(ServiceSchedule::getHalls()),
                Tables\Filters\Filter::make('start_date')
                    ->label('Дата')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')->label('Дата от'),
                        Forms\Components\DatePicker::make('date_to')->label('Дата до'),
                    ])
                    ->query(function (Eloquent\Builder $query, array $data) {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Eloquent\Builder $query, $date): Eloquent\Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn (Eloquent\Builder $query, $date): Eloquent\Builder => $query->whereDate('start_date', '<=', $date),
                            );
                    }),
                Tables\Filters\Filter::make('start_time')
                    ->label('Время')
                    ->form([
                        Forms\Components\TimePicker::make('time_from')->label('Время от'),
                        Forms\Components\TimePicker::make('time_to')->label('Время до'),
                    ])
                    ->query(function (Eloquent\Builder $query, array $data) {
                        return $query
                            ->when(
                                $data['time_from'],
                                fn (Eloquent\Builder $query, $time): Eloquent\Builder => $query->whereTime('start_time', '>=', $time),
                            )
                            ->when(
                                $data['time_to'],
                                fn (Eloquent\Builder $query, $time): Eloquent\Builder => $query->whereTime('start_time', '<=', $time),
                            );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
                Tables\Actions\BulkAction::make('copy_schedule')
                    ->label('Копировать выделенные на новую дату')
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label('Копировать с даты')
                            ->reactive()
                            ->required(),
                    ])
                    ->action(function (Eloquent\Collection $selectedRecords, array $data) {
                        DB::transaction(function () use ($selectedRecords, $data) {
                            $selectedRecords = $selectedRecords->sortBy('start_date');
                            $firstRecord = $selectedRecords->first();
                            $diff = \Carbon\Carbon::parse($data['from_date'])
                                ->diffInDays($firstRecord->start_date);

                            $selectedRecords->each(
                                function (Eloquent\Model $selectedRecord) use ($diff) {
                                    ServiceSchedule::create([
                                        'service_id' => $selectedRecord->service_id,
                                        'start_date' => \Carbon\Carbon::parse($selectedRecord->start_date)->addDays($diff),
                                        'start_time' => $selectedRecord->start_time,
                                        'hall' => $selectedRecord->hall,
                                        'places_count_total' => $selectedRecord->places_count_total,
                                        'places_count_left' => $selectedRecord->places_count_total,
                                        'complexity' => $selectedRecord->complexity,
                                    ]);
                                }
                            );
                        });
                    })
                    ->infolist(function (Eloquent\Collection $selectedRecords) {
                        //
                    })
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ServiceScheduleUsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceSchedules::route('/'),
            'create' => Pages\CreateServiceSchedule::route('/create'),
            'edit' => Pages\EditServiceSchedule::route('/{record}/edit'),
        ];
    }
}
