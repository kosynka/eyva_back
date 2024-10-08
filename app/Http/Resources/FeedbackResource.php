<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'stars' => $this->stars,
            'body' => $this->body,
            'program' => (new ProgramResource($this->whenLoaded('program'))),
            'service' => (new ServiceResource($this->whenLoaded('service'))),
            'enrollment' => (new ServiceScheduleResource($this->whenLoaded('enrollment'))),
        ];
    }
}
