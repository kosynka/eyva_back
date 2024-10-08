<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'Пользователи';
    protected static ?string $modelLabel = 'Пользователь';
    protected static ?string $pluralModelLabel = 'Пользователи';
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $activeNavigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', User::ROLE_USER);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Имя')
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->required()
                    ->label('Телефон')
                    ->maxLength(11)
                    ->regex('/^77[0-9]{9}$/')
                    ->placeholder('77474673832')
                    ->unique(User::class, 'phone', ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Такой номер телефона уже существует',
                        'regex' => 'Неправильный формат телефона',
                    ]),
                Forms\Components\DatePicker::make('birth_date')
                    ->label('Дата рождения')
                    ->rules(['date', 'after:1900-01-01']),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->minLength(6)
                    ->maxLength(20)
                    ->rules([
                        'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
                    ])
                    ->label('Пароль')
                    ->required(fn($context) => $context === 'create')
                    ->placeholder('Введите пароль')
                    ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn($state) => filled($state))
                    ->validationMessages([
                        'regex' => 'Неверный формат пароля. Пароль должен содержать как минимум одну строчную букву, одну заглавную букву, одну цифру и один специальный символ.',
                    ]),
                Forms\Components\TextInput::make('balance')
                    ->label('Баланс')
                    ->default(0)
                    ->reactive()
                    ->readonly(),
                Forms\Components\FileUpload::make('photo')
                    ->label('Фото')
                    ->image()
                    ->openable()
                    ->downloadable()
                    ->imageEditor()
                    ->previewable()
                    ->directory('user_photos')
                    ->maxSize(10240)
                    ->validationMessages([
                        'required' => 'Обязательное поле',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Фото')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Имя')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон')
                    ->formatStateUsing(fn($state) => preg_replace(
                        '~(\d{1})(\d{3})(\d{3})(\d{4})~',
                        '+$1 $2 $3 $4',
                        $state,
                    ))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('День рождения')
                    ->dateTime('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Баланс')
                    ->formatStateUsing(fn($state) => number_format( $state, 0, ' ', ' '))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата регистрации')
                    ->dateTime('Y M d(D), h:i:s')
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
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UserTransactionsRelationManager::class,
            RelationManagers\UserProgramsRelationManager::class,
            RelationManagers\UserAbonnementsRelationManager::class,
            RelationManagers\UserServiceScheduleRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
