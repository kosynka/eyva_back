<?php

namespace App\Models;

use App\Models\Contracts\ModelWithFileObject;
use App\Models\Traits\HasFileObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[\Illuminate\Database\Eloquent\Attributes\ObservedBy([\App\Observers\FileObjectObserver::class])]
class ServicePhoto extends Model implements ModelWithFileObject
{
    use HasFactory, HasFileObject;

    const TYPE_PHOTO = 1;
    const TYPE_VIDEO = 2;

    protected $fillable = [
        'service_id',
        'type',
        'link',
        'preview',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function fileTypeAttributeName(): string
    {
        return 'type';
    }

    public function fileLinkAttributeName(): string
    {
        return 'link';
    }

    public function needPreview(): bool
    {
        return true;
    }
}
