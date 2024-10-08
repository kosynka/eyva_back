<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceScheduleResource extends JsonResource
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
            'start_date' => $this->start_date,
            'start_time' => $this->start_time,
            'places_count_left' => $this->places_count_left,
            'complexity' => $this->complexity,
            'my_feedback' => new FeedbackResource($this->whenLoaded('myFeedback')),
            'service' => new ServiceResource($this->whenLoaded('service')),
        ];
    }
}
