<?php

namespace App\Models\Traits;

use App\Enums\FileEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

trait HasPhotosAttribute
{
    public function photosObject(): Attribute
    {
        return Attribute::make(
            get: function (): array|null {
                if ($this->photos === '[]') {
                    return [];
                }

                return collect($this->photos)->map(function ($photo) {
                    return [
                        'preview' => isset($photo['preview']) ? Storage::disk('public')->url($photo['preview']) : null,
                        'url' => Storage::disk('public')->url($photo['link']),
                        'fileType' => FileEnum::getType($photo['type']),
                    ];
                })->toArray();
            }
        );
    }
}
