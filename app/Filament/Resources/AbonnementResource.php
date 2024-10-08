<?php

namespace App\Filament\Resources;

use App\Enums\BuyableStatusEnum;
use App\Filament\Resources\AbonnementResource\Pages;
use App\Filament\Resources\AbonnementResource\RelationManagers;
use App\Models\Abonnement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AbonnementResource extends Resource
{
    protected static ?string $model = Abonnement::class;
    protected static ?string $navigationLabel = 'Абонементы';
    protected static ?string $modelLabel = 'Абонемент';
    protected static ?string $pluralModelLabel = 'Абонементы';
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $activeNavigationIcon = 'heroicon-s-ticket';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Не забудьте скрыть абонемент перед редактированием')
                    ->description('Скрытые абонементы не будут доступны в приложении, это поможет избежать ошибок')
                    ->icon('heroicon-o-exclamation-circle')
                    ->headerActions([
                        \App\Filament\Actions\ComponentActions\ChangeStatus::make([
                            'related_model' => 'programServices',
                            'message' => 'Добавьте хотя бы одну услугу',
                            'status' => 'danger',
                        ]),
                    ])
                    ->hidden(function (?Model $record) {
                        return $record === null || $record->status === BuyableStatusEnum::DISABLED_KEY;
                    }),
                Forms\Components\Select::make('status')
                    ->label('Статус')
                    ->options(BuyableStatusEnum::getStatuses())
                    ->default(BuyableStatusEnum::DISABLED_KEY)
                    ->disabled(),
                Forms\Components\TextInput::make('title')
                    ->label('Название')
                    ->type('text')
                    ->required(),
                Forms\Components\TextInput::make('duration_in_days')
                    ->label('Длительность в днях')
                    ->type('number')
                    ->minValue(1)
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->label('Цена')
                    ->type('number')
                    ->minValue(1)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('minutes', $state * 6);
                    })
                    ->required(),
                Forms\Components\TextInput::make('minutes')
                    ->label('Количество минут')
                    ->live()
                    ->type('number')
                    ->minValue(1)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(function ($state) {
                        if ($state === BuyableStatusEnum::DISABLED_KEY) {
                            return 'danger';
                        } else if ($state === BuyableStatusEnum::ENABLED_KEY) {
                            return 'success';
                        }

                        return 'secondary';
                    })
                    ->label('Статус')
                    ->formatStateUsing(fn($state) => BuyableStatusEnum::getStatusText($state))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_in_days')
                    ->label('Длительность в днях')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('minutes')
                    ->label('Количество минут')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    \App\Filament\Actions\TableActions\MakeEnabledAction::make(),
                    \App\Filament\Actions\TableActions\MakeDisabledAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AbonnementPresentsRelationManager::class,
            RelationManagers\AbonnementPhotosRelationManager::class,
            RelationManagers\AbonnementUsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbonnements::route('/'),
            'create' => Pages\CreateAbonnement::route('/create'),
            'edit' => Pages\EditAbonnement::route('/{record}/edit'),
        ];
    }
}
