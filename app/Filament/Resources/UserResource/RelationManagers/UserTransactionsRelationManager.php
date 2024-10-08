<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Transaction;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class UserTransactionsRelationManager extends RelationManager
{
    protected static ?string $title = 'Транзакции';
    protected static string $relationship = 'transactions';
    protected static ?string $inverseRelationship = 'users';

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
            ->modelLabel('транзакция')
            ->pluralModelLabel('транзакции')
            ->recordTitleAttribute('type')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn(Transaction $record) => $record->getType())
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Сумма')
                    ->formatStateUsing(fn(Transaction $record) => $record->getAmount())
                    ->sortable(),
                Tables\Columns\TextColumn::make(name: 'status')
                    ->label('Статус')
                    ->formatStateUsing(fn(Transaction $record) => $record->getStatus())
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Комментарий')
                    ->wrap(),
                Tables\Columns\TextColumn::make(name: 'created_at')
                    ->label('Дата и время')
                    ->formatStateUsing(fn($state) => $state->format('Y-m-d H:i:s'))
                    ->sortable(),
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
