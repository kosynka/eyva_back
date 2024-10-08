<?php

namespace App\Filament\Resources\ServiceScheduleResource\RelationManagers;

use App\Filament\Resources\UserResource;
use App\Models\UserServiceSchedule;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class ServiceScheduleUsersRelationManager extends RelationManager
{
    protected static ?string $title = 'Записавшиеся люди';
    protected static string $relationship = 'users';
    protected static ?string $inverseRelationship = 'serviceSchedule';

    public function form(Form $form): Form
    {
        return $form->schema([
            //
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel('запись')
            ->pluralModelLabel('записи')
            ->columns([
                Tables\Columns\ImageColumn::make('user.photo')
                    ->label('Фото')
                    ->circular(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->url(fn ($record) => UserResource::getUrl('edit', ['record' => $record->user]), true),
                Tables\Columns\TextColumn::make('type')
                    ->label('Тип оплаты')
                    ->formatStateUsing(fn (UserServiceSchedule $record) => $record->getType()),
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (UserServiceSchedule $record) => $record->getStatus()),
            ])
            ->filters([
                Tables\Filters\Filter::make('current')
                    ->label('Текущие')
                    ->query(fn ($query) => $query->where('status', UserServiceSchedule::STATUS_ENROLLED)),
                
                Tables\Filters\Filter::make('history')
                    ->label('История')
                    ->query(fn ($query) => $query->whereIn('status', [
                        UserServiceSchedule::STATUS_FINISHED,
                        UserServiceSchedule::STATUS_SKIPPED
                ])),
            ])
            ->headerActions([
            ])
            ->actions([
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('Finished')
                ->label('Отметить как завершенное')
                ->action(function (Collection $records) {
                    $ids = $records->pluck('id');
                    UserServiceSchedule::whereIn('id', $ids)->update(['status' => UserServiceSchedule::STATUS_FINISHED]);
                })
                ->deselectRecordsAfterCompletion()
                ->color('success'),
            ]);
    }
}
