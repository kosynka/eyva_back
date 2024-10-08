<?php

namespace App\Filament\Resources\ProgramResource\RelationManagers;

use App\Enums\FileEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class ProgramPhotosRelationManager extends RelationManager
{
    protected static ?string $title = 'Медиа';
    protected static string $relationship = 'photos';
    protected static ?string $inverseRelationship = 'program';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Тип')
                    ->options(FileEnum::getTypesWithText())
                    ->required(),
                Forms\Components\FileUpload::make('link')
                    ->label('Файл')
                    ->openable()
                    ->downloadable()
                    ->previewable()
                    ->directory('program_photos')
                    ->required(),
                Forms\Components\FileUpload::make('preview')
                    ->label('Превью для видео')
                    ->image()
                    ->openable()
                    ->imageEditor()
                    ->downloadable()
                    ->previewable()
                    ->directory('program_photos')
                    ->required(fn(Forms\Get $get) => $get('type') === FileEnum::VIDEO_KEY->value)
                    ->validationMessages([
                        'required' => 'Необходимо загрузить превью для видео',
                    ]),
            ]);
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
