<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProgramResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $daysUntilExpiration = ceil(now()->diffInDays($this->expiration_date));

        return [
            'id' => $this->id,
            'program_id' => $this->program_id,
            'expiration_date' => $this->expiration_date,
            'days_until_expiration' => $daysUntilExpiration,
            'visits' => $this->program_services_sum_visits,
            'total_visits' => $this->program_services_sum_old_visits,

            'title' => $this->old_title,
            'description' => $this->old_description,
            // 'requirements' => $this->old_requirements,
            'duration_in_days' => $this->old_duration_in_days,
            'was_updated' => $this->wasUpdated(),

            'photos' => $this->photosObject,
            'services' => UserProgramServiceResource::collection($this->whenLoaded('programServices')),
            'program' => new ProgramResource($this->whenLoaded('program')),
        ];
    }
}
