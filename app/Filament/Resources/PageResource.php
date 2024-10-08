<?php

namespace App\Filament\Resources;

use App\Enums\FileEnum;
use App\Filament\Resources\PageResource\Pages;
use App\Models\PageElement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = PageElement::class;
    protected static ?string $navigationLabel = 'Элементы страницы';
    protected static ?string $modelLabel = 'Элемент страницы';
    protected static ?string $pluralModelLabel = 'Элементы страницы';
    protected static ?string $navigationIcon = 'heroicon-o-information-circle';
    protected static ?string $activeNavigationIcon = 'heroicon-s-information-circle';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('page_type')
                    ->label('Тип страницы')
                    ->options(PageElement::getTypes())
                    ->required(),
                Forms\Components\TextInput::make('key')
                    ->label('Ключ')
                    ->required(),
                Forms\Components\Select::make('file_mime_type')
                    ->label('Тип файла')
                    ->options(FileEnum::getTypesWithText())
                    ->required(function (Forms\Get $get) {
                        return $get('file');
                    })
                    ->validationMessages([
                        'required' => 'Необходимо выбрать тип файла',
                    ]),
                Forms\Components\FileUpload::make('preview')
                    ->label('Превью для видео')
                    ->image()
                    ->openable()
                    ->downloadable()
                    ->imageEditor()
                    ->previewable()
                    ->directory('page_files')
                    ->required(function(Forms\Get $get) {
                        return $get('file_mime_type') === FileEnum::VIDEO_KEY->value;
                    })
                    ->validationMessages([
                        'required' => 'Необходимо загрузить превью для видео',
                    ]),
                Forms\Components\Textarea::make('text')
                    ->label('Текст'),
                Forms\Components\FileUpload::make('file')
                    ->label('Файл')
                    ->openable()
                    ->downloadable()
                    ->previewable()
                    ->directory('page_files')
                    ->required(function (Forms\Get $get) {
                        return $get('file_mime_type');
                    })
                    ->validationMessages([
                        'required' => 'Необходимо загрузить файл',
                    ]),
                Forms\Components\TextInput::make('weight')
                    ->label('Очерёдность')
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('page_type')
                    ->label('Тип страницы')
                    ->formatStateUsing(fn($state) => PageElement::getTypeText($state))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('key')
                    ->label('Ключ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('text')
                    ->label('Текст')
                    ->limit(40)
                    ->searchable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('file_mime_type')
                    ->label('Тип файла')
                    ->formatStateUsing(fn($state) => FileEnum::getTypeWithText($state))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight')
                    ->label('Очерёдность')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
