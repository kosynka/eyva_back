<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoryRoutesRelationManager extends RelationManager
{
    protected static ?string $title = 'Направления';
    protected static string $relationship = 'routes';
    protected static ?string $inverseRelationship = 'category';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->label('Название')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Описание')
                    ->maxLength(255),
                Forms\Components\Hidden::make('type')
                    ->default(function ($record) {
                        return Category::TYPE_ROUTE;
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel('направление')
            ->pluralModelLabel('направления')
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->url(fn ($record) => CategoryResource::getUrl('edit', ['record' => $record]), true),
                Tables\Columns\TextColumn::make('description')
                    ->label('Описание')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Тип')
                    ->sortable()
                    ->formatStateUsing(fn($state) => match ($state) {
                        Category::TYPE_CATEGORY => 'Категория',
                        Category::TYPE_ROUTE => 'Направление',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AssociateAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(function (Builder $query) {
                        return $query->where(['type' => Category::TYPE_ROUTE]);
                    }),
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\DissociateAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DissociateBulkAction::make(),
            ]);
    }
}
