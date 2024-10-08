<?php

namespace App\Filament\Resources\AbonnementResource\RelationManagers;

use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AbonnementPresentsRelationManager extends RelationManager
{
    protected static ?string $title = 'Подарки';
    protected static string $relationship = 'presents';
    protected static ?string $inverseRelationship = 'abonnements';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('text')
                    ->label('Текст')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('visits')
                    ->label('Посещений')
                    ->type('number')
                    ->minValue(1)
                    ->required(),
                Forms\Components\Select::make('service_id')
                    ->label('Услуга')
                    ->relationship('service', 'title')
                    ->options(function () {
                        return Service::get()->pluck('title', 'id');
                    })
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel('подарок')
            ->pluralModelLabel('подарки')
            ->recordTitleAttribute('service.title')
            ->columns([
                Tables\Columns\TextColumn::make('service.title')
                    ->label('Услуга'),
                Tables\Columns\TextColumn::make('text')
                    ->limit(30)
                    ->label('Текст'),
                Tables\Columns\TextColumn::make('visits')
                    ->label('Посещений'),
            ])
            ->filters([
                //
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
