<?php

namespace App\Models\Traits;

use App\Enums\FileEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

trait HasFileObject
{
    public string $previewAttributeName = 'preview';

    public function getFileLinkAttribute(): string|null
    {
        if (!$this->hasAttribute($this->fileLinkAttributeName())) {
            throw new \Exception(
                "'{$this->fileLinkAttributeName()}' - file property name not found in " . get_class($this),
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }

        return $this->attributes[$this->fileLinkAttributeName()];
    }

    public function getFileTypeAttribute(): string|null
    {
        if ($this->fileTypeAttributeName() instanceof FileEnum) {
            return FileEnum::getType($this->fileTypeAttributeName()->value);
        }

        if (!$this->hasAttribute($this->fileTypeAttributeName())) {
            throw new \Exception(
                "'{$this->fileTypeAttributeName()}' - file property type not found in " . get_class($this),
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }

        return FileEnum::getType($this->attributes[$this->fileTypeAttributeName()]);
    }

    public function getPreviewLinkAttribute(): string|null
    {
        if ($this->getFileTypeAttribute() !== FileEnum::VIDEO_NAME || $this->needPreview() === false) {
            return null;
        }

        if (!$this->hasAttribute($this->previewAttributeName)) {
            throw new \Exception(
                "'{$this->previewAttributeName}' - file property name not found in " . get_class($this),
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }

        return $this->attributes[$this->previewAttributeName];
    }

    public function fileObject(): Attribute
    {
        return Attribute::make(
            get: function (): array|null {
                if ($this->getFileLinkAttribute() !== null) {
                    return [
                        'preview' => Storage::disk('public')->url($this->getPreviewLinkAttribute()),
                        'url' => Storage::disk('public')->url($this->getFileLinkAttribute()),
                        'fileType' => $this->getFileTypeAttribute(),
                    ];
                }

                return null;
            }
        );
    }

    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => ucfirst($value),
            set: fn(string $value) => strtolower($value),
        );
    }
}
