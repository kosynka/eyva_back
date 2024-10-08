<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserServiceScheduleRelationManager extends RelationManager
{
    protected static ?string $title = 'Расписание';
    protected static string $relationship = 'userServiceSchedules';
    protected static ?string $inverseRelationship = 'schedules';
    protected $schedulesToCopy = null;

    public function form(Form $form): Form
    {
        return $form->schema([
            // 
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel(label: 'расписание')
            ->pluralModelLabel('записей')
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('schedule.start_date')
                    ->label('Дата начала')
                    ->date(),
                Tables\Columns\TextColumn::make('schedule.start_time')
                    ->label('Время начала')
                    ->time(),
                Tables\Columns\TextColumn::make('getStatus')
                    ->label('Статус')
                    ->sortable(),
                Tables\Columns\TextColumn::make('getType')
                    ->label('Тип')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('current')
                    ->label('Текущие')
                    ->query(fn (Builder $query): Builder => $query->whereHas('schedule', fn (Builder $query) =>
                        $query->where('start_date', '>=', now()->toDateString())
                    )),
                Tables\Filters\Filter::make('history')
                    ->label('История')
                    ->query(fn (Builder $query): Builder => $query->whereHas('schedule', fn (Builder $query) =>
                        $query->where('start_date', '<', now()->toDateString())
                    )),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
