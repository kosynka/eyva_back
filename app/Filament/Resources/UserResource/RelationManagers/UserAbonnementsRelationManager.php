<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\AbonnementResource;
use App\Repositories\ServiceRepository;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class UserAbonnementsRelationManager extends RelationManager
{
    protected static ?string $title = 'Абонементы';
    protected static string $relationship = 'userAbonnements';
    protected static ?string $inverseRelationship = 'abonnements';
    protected static ?string $recordTitleAttribute = 'abonnement.title';

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
            ->modelLabel('абонемент')
            ->pluralModelLabel('абонементы')
            ->recordTitleAttribute('old_title')
            ->columns([
                Tables\Columns\TextColumn::make('old_title')
                    ->label('Абонемент')
                    ->sortable()
                    ->searchable()
                    ->url(fn($record) => AbonnementResource::getUrl('edit', ['record' => $record]), true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата покупки')
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('Y-m-d'))
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
            ->defaultSort('expiration_date', 'desc')
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
