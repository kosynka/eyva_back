<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            // 'requirements' => $this->requirements,
            'duration_in_days' => $this->duration_in_days,
            'price' => $this->price,
            'photos' => FileResource::collection($this->whenLoaded('photos')),
            'services' => ProgramServiceResource::collection($this->whenLoaded('programServices')),
        ];
    }
}
