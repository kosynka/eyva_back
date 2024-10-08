<?php

namespace App\Filament\Resources\ProgramResource\RelationManagers;

use App\Filament\Resources\UserResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProgramUsersRelationManager extends RelationManager
{
    protected static ?string $title = 'Пользовательские программы';
    protected static string $relationship = 'userPrograms';
    protected static ?string $inverseRelationship = 'programs';

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
            ->modelLabel('пользовательскую программу')
            ->pluralModelLabel('пользовательские программы')
            ->recordTitleAttribute('user.name')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->sortable()
                    ->searchable()
                    ->url(fn($record) => UserResource::getUrl('edit', ['record' => $record->user]), true),
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
                    ->getStateUsing(function ($record) {
                        foreach ($record->programServices as $userPS) {
                            $servicesByVisits[] = "{$userPS->programService->service->title}({$userPS->visits}/{$userPS->old_visits})";
                        }

                        $servicesByVisits = implode("\r\r \f\f \n\n", $servicesByVisits);

                        return $servicesByVisits;
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
