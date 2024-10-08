<?php

namespace App\Filament\Resources\ProgramResource\RelationManagers;

use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProgramServicesRelationManager extends RelationManager
{
    protected static ?string $title = 'Услуги';
    protected static string $relationship = 'programServices';
    protected static ?string $inverseRelationship = 'programs';

    public function form(Form $form): Form
    {
        return $form->schema([
                Forms\Components\TextInput::make('visits')
                    ->label('Посещений')
                    ->type('number')
                    ->minValue(1)
                    ->required(),
                Forms\Components\Select::make('service_id')
                    ->label('Услуга')
                    ->relationship(
                        'service',
                        'title',
                        function (Builder $query) {
                            return $query->where('type', '<>', Service::TYPE_MASTERCLASS);
                        }
                    )
                    ->options(function ($get, $record) {
                        return Service::where('type', '<>', Service::TYPE_MASTERCLASS)
                            ->get()
                            ->pluck('title', 'id');
                    })
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel('услугу')
            ->pluralModelLabel('услуги')
            ->recordTitleAttribute('service.title')
            ->columns([
                Tables\Columns\TextColumn::make('service.title')
                    ->label('Услуга'),
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
