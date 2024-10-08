<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\ProgramResource;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class UserProgramsRelationManager extends RelationManager
{
    protected static ?string $title = 'Программы';
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
            ->modelLabel('программу')
            ->pluralModelLabel('программы')
            ->columns([
                Tables\Columns\TextColumn::make('old_title')
                    ->label('Программа')
                    ->sortable()
                    ->searchable()
                    ->url(fn($record) => ProgramResource::getUrl('edit', ['record' => $record]), true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата покупки')
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('Y-m-d'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->label('Дата окончания')
                    ->sortable(),
                Tables\Columns\TextColumn::make('old_price')
                    ->label('Цена')
                    ->sortable(),
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
