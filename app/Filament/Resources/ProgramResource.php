<?php

namespace App\Filament\Resources;

use App\Enums\BuyableStatusEnum;
use App\Filament\Resources\ProgramResource\Pages;
use App\Filament\Resources\ProgramResource\RelationManagers;
use App\Models\Program;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;
    protected static ?string $navigationLabel = 'Программы';
    protected static ?string $modelLabel = 'Программа';
    protected static ?string $pluralModelLabel = 'Программы';
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $activeNavigationIcon = 'heroicon-s-squares-2x2';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Не забудьте скрыть программу перед редактированием')
                    ->description('Скрытые программы не будут доступны в приложении, это поможет избежать ошибок')
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
                Forms\Components\Textarea::make('description')
                    ->label('Описание')
                    ->maxLength(65535),
                // Forms\Components\TextInput::make('requirements')
                //     ->label('Требования')
                //     ->maxLength(65535),
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
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Описание')
                    ->limit(40)
                    ->sortable()
                    ->searchable(),
                // Tables\Columns\TextColumn::make('requirements')
                //     ->label('Требования')
                //     ->limit(40)
                //     ->sortable()
                //     ->searchable(),
                Tables\Columns\TextColumn::make('duration_in_days')
                    ->label('Длительность')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
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
                    \App\Filament\Actions\TableActions\MakeEnabledAction::make([
                        'related_model' => 'programServices',
                        'message' => 'Добавьте хотя бы одну услугу',
                        'status' => 'danger',
                    ]),
                    \App\Filament\Actions\TableActions\MakeDisabledAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProgramServicesRelationManager::class,
            RelationManagers\ProgramPhotosRelationManager::class,
            RelationManagers\ProgramUsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrograms::route('/'),
            'create' => Pages\CreateProgram::route('/create'),
            'edit' => Pages\EditProgram::route('/{record}/edit'),
        ];
    }
}
