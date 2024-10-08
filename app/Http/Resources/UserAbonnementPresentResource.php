<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAbonnementPresentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
            'id' => $this->id,
            'visits' => $this->visits,
            'total_visits' => $this->old_visits,
            'text' => $this->old_text,
            'service' => new ServiceResource($this->whenLoaded('service')),
        ];
    }
}
