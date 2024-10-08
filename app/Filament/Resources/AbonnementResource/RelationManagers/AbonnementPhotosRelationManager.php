<?php

namespace App\Filament\Resources\AbonnementResource\RelationManagers;

use App\Enums\FileEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class AbonnementPhotosRelationManager extends RelationManager
{
    protected static ?string $title = 'Медиа';
    protected static string $relationship = 'photos';
    protected static ?string $inverseRelationship = 'abonnements';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('link')
                    ->label('Файл')
                    ->image()
                    ->openable()
                    ->downloadable()
                    ->previewable()
                    ->imageEditor()
                    ->maxSize(10240)
                    ->directory('abonnement_photos')
                    ->required(),
            ]);
    }

    public function beforeCreate(array $data): array
    {
        $data['type'] = FileEnum::PHOTO_KEY->value;

        return $data;
    }

    public function beforeSave(array $data): array
    {
        $data['type'] = FileEnum::PHOTO_KEY->value;

        return $data;
    }

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel('медиа')
            ->pluralModelLabel('медиа')
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn($state) => FileEnum::getTypeWithText($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('link')
                    ->label('Ссылка')
                    ->formatStateUsing(fn($state) => Storage::disk('public')->url($state))
                    ->url(
                        fn ($record) => Storage::disk('public')->url($record->link),
                        true,
                    ),
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
