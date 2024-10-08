<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JsonResourceWithPhoto extends JsonResource
{
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }

    protected function getFile(string $file = null): array|null
    {
        if ($file) {
            return [
                'fileType' => 'photo',
                'url' => url($file),
            ];
        }

        return null;
    }
}
