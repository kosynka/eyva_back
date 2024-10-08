<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PageElementResource extends JsonResourceWithPhoto
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'key' => $this->key,
            'text' => $this->text,
            'file' => $this->file_object,
        ];
    }
}
