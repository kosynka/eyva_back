<?php

namespace App\Filament\Resources\AbonnementResource\RelationManagers;

use App\Filament\Resources\UserResource;
use App\Repositories\ServiceRepository;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AbonnementUsersRelationManager extends RelationManager
{
    protected static ?string $title = 'Пользовательские абонементы';
    protected static string $relationship = 'userAbonnements';
    protected static ?string $inverseRelationship = 'abonnements';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel('пользовательский абонемент')
            ->pluralModelLabel('пользовательские абонементы')
            ->recordTitleAttribute('user.name')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->sortable()
                    ->searchable()
                    ->url(fn ($record) => UserResource::getUrl('edit', ['record' => $record->user]), true),
                Tables\Columns\TextColumn::make('old_title')
                    ->label('Название')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата покупки')
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->format('Y-m-d'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->label('Дата окончания')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('old_price')
                    ->label('Цена')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('minutes')
                    ->label('Посещений по услугам')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        foreach ((new ServiceRepository())->getAllGroupedByDuration() as $minut => $serviceList) {
                            $visits = floor($record->minutes / $minut);
                            $total_visits = floor($record->old_minutes / $minut);

                            $servicesByMinutes[] = "{$minut} минут({$visits}/{$total_visits})";
                        }

                        $servicesByMinutes = implode("\r\r \f\f \n\n", $servicesByMinutes);

                        return $servicesByMinutes;
                    }),
                Tables\Columns\TextColumn::make('presents')
                    ->label('Подарки')
                    ->getStateUsing(function ($record) {
                        return $record->presents->map(function ($present) {
                            return "{$present->abonnementPresent->service->title}({$present->visits}/{$present->old_visits})";
                        })->implode("\r\r \f\f \n\n");
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}
