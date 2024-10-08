<?php

namespace App\Filament\Resources\ServiceResource\RelationManagers;

use App\Models\ServiceSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceSchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';
    protected static ?string $title = 'Расписание';
    protected static ?string $inverseRelationship = 'service';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('service_id')
                    ->label('Услуга')
                    ->relationship('service', 'title')
                    ->required(),
                Forms\Components\Select::make('hall')
                    ->label('Зал')
                    ->options(ServiceSchedule::getHalls())
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->label('Дата')
                    ->required(),
                Forms\Components\TimePicker::make('start_time')
                    ->label('Время')
                    ->required(),
                Forms\Components\TextInput::make('places_count_left')
                    ->label('Осталось мест')
                    ->numeric()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel('расписание')
            ->pluralModelLabel('расписания')
            ->columns([
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
            ])
            ->defaultSort('start_date', 'desc')
            ->defaultSort('start_time', 'desc')
            ->filters([
                Tables\Filters\Filter::make('status')
                    ->label('Статус')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                'current' => 'Текущие',
                                'history' => 'История',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['status'])) {
                            if ($data['status'] === 'current') {
                                return $query->where('start_date', '>=', now());
                            } elseif ($data['status'] === 'history') {
                                return $query->where('start_date', '<', now());
                            }
                        }
    
                        return $query;
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
