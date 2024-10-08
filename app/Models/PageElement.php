<?php

namespace App\Models;

use App\Models\Contracts\ModelWithFileObject;
use App\Models\Traits\HasFileObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\ObservedBy([\App\Observers\FileObjectObserver::class])]
class PageElement extends Model implements ModelWithFileObject
{
    use HasFactory, HasFileObject;

    public const PAGE_TYPE_ABOUT_US = 1;
    public const PAGE_TYPE_MAIN = 2;

    protected $fillable = [
        'page_type',
        'key',
        'text',
        'file',
        'file_mime_type',
        'preview',
        'weight',
    ];

    public static function getTypes(): array
    {
        return [
            self::PAGE_TYPE_ABOUT_US => 'О нас',
            self::PAGE_TYPE_MAIN => 'Главная',
        ];
    }

    public static function getTypeText(int $type): string
    {
        return match ($type) {
            self::PAGE_TYPE_ABOUT_US => 'О нас',
            self::PAGE_TYPE_MAIN => 'Главная',
        };
    }

    public function fileTypeAttributeName(): string
    {
        return 'file_mime_type';
    }

    public function fileLinkAttributeName(): string
    {
        return 'file';
    }

    public function needPreview(): bool
    {
        return true;
    }
}
