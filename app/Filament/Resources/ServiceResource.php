<?php

namespace App\Filament\Resources;

use App\Enums\ComplexityEnum;
use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static ?string $navigationLabel = 'Услуги';
    protected static ?string $modelLabel = 'Услуги';
    protected static ?string $pluralModelLabel = 'Услуги';
    protected static ?string $navigationIcon = 'heroicon-o-square-2-stack';
    protected static ?string $activeNavigationIcon = 'heroicon-s-square-2-stack';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Тип')
                    ->options(Service::getTypes())
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->label('Название')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('duration')
                    ->label('Продолжительность')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(1440)
                    ->required(),
                Forms\Components\TextInput::make('requirements')
                    ->label('Требования')
                    ->maxLength(65535),
                Forms\Components\TextInput::make('price')
                    ->label('Цена')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                Forms\Components\Select::make('complexity')
                    ->label('Сложность')
                    ->options(ComplexityEnum::getAllWithText()),
                Forms\Components\Textarea::make('description')
                    ->label('Описание'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn($state) => Service::getTypeText($state))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('complexity')
                    ->label('Сложность')
                    ->formatStateUsing(fn($state) => ComplexityEnum::getOneWithText($state))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Описание')
                    ->limit(40)
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ServiceCategoriesRelationManager::class,
            RelationManagers\ServicePhotosRelationManager::class,
            RelationManagers\ServiceSchedulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
