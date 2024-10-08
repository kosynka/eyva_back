<?php

namespace App\Models\Contracts;

use App\Enums\FileEnum;

interface ModelWithFileObject
{
    public function fileTypeAttributeName(): string|FileEnum;

    public function fileLinkAttributeName(): string;

    public function needPreview(): bool;
}
