<?php

namespace App\Models;

use App\Models\Contracts\ModelWithFileObject;
use App\Models\Traits\HasFileObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[\Illuminate\Database\Eloquent\Attributes\ObservedBy([\App\Observers\FileObjectObserver::class])]
class AbonnementPhoto extends Model implements ModelWithFileObject
{
    use HasFactory, HasFileObject;

    protected $fillable = [
        'abonnement_id',
        'type',
        'link',
        'preview',
    ];

    public function abonnement(): BelongsTo
    {
        return $this->belongsTo(Abonnement::class, 'abonnement_id');
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
