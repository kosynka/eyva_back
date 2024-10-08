<?php

namespace App\Models;

use App\Enums\FileEnum;
use App\Models\Contracts\ModelWithFileObject;
use App\Models\Traits\HasFileObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[\Illuminate\Database\Eloquent\Attributes\ObservedBy([\App\Observers\FileObjectObserver::class])]
class Category extends Model implements ModelWithFileObject
{
    use HasFactory, HasFileObject;

    const TYPE_CATEGORY = 1;
    const TYPE_ROUTE = 2;

    protected $with = ['routes'];
    protected $fillable = [
        'type',
        'title',
        'description',
        'parent_id',
        'photo',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function routes(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function categoryServices(): HasMany
    {
        return $this->hasMany(CategoryService::class, 'category_id');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'category_services');
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_CATEGORY => 'Категория',
            self::TYPE_ROUTE => 'Направление',
        ];
    }

    public function getType(): string
    {
        return match ($this->type) {
            self::TYPE_CATEGORY => 'Категория',
            self::TYPE_ROUTE => 'Направление',
        };
    }

    public static function getTypeText(int $type): string
    {
        return match ($type) {
            self::TYPE_CATEGORY => 'Категория',
            self::TYPE_ROUTE => 'Направление',
        };
    }

    public function fileTypeAttributeName(): FileEnum
    {
        return FileEnum::PHOTO_KEY;
    }

    public function fileLinkAttributeName(): string
    {
        return 'photo';
    }

    public function needPreview(): bool
    {
        return false;
    }
}
