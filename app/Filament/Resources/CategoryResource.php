<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationLabel = 'Категории';
    protected static ?string $modelLabel = 'Категория';
    protected static ?string $pluralModelLabel = 'Категории';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $activeNavigationIcon = 'heroicon-s-rectangle-stack';
    protected static ?int $navigationSort = 2;

    protected static function booted()
    {
        static::saving(function ($category) {
            if ($category->parent_id) {
                $category->type = Category::TYPE_ROUTE;
            } else {
                $category->type = Category::TYPE_CATEGORY;
            }
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Название')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\Hidden::make('type')
                    ->default(function ($record) {
                        return $record && $record->parent_id ? Category::TYPE_ROUTE : Category::TYPE_CATEGORY;
                    }),
                Forms\Components\Select::make('type')
                    ->label('Тип')
                    ->options(Category::getTypes())
                    ->disabled(),
                Forms\Components\Select::make('parent_id')
                    ->label('Родительская категория')
                    ->relationship(
                        'category',
                        'title',
                        function (Builder $query) {
                            return $query->where('type', Category::TYPE_CATEGORY);
                        }
                    )
                    ->options(function ($record) {
                        $query = Category::where('type', Category::TYPE_CATEGORY);

                        if ($record) {
                            $query->where('id', '!=', $record->id);
                        }

                        return $query->get()->pluck('title', 'id');
                    })
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        $set('type', $state ? Category::TYPE_ROUTE : Category::TYPE_CATEGORY);
                    }),
                Forms\Components\FileUpload::make('photo')
                    ->label('Фото')
                    ->image()
                    ->openable()
                    ->imageEditor()
                    ->downloadable()
                    ->previewable()
                    ->directory('category_photos')
                    ->maxSize(10240)
                    ->required(fn($context) => $context === 'create')
                    ->validationMessages([
                        'required' => 'Обязательное поле',
                    ]),
                Forms\Components\Textarea::make('description')
                    ->label('Описание')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn($state) => Category::getTypeText($state))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Описание')
                    ->limit(40)
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
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CategoryRoutesRelationManager::class,
            RelationManagers\CategoryServicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
