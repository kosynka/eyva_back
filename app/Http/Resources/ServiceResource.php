<?php

namespace App\Http\Resources;

use App\Models\Feedback;
use App\Repositories\UserAbonnementRepository;
use App\Repositories\UserProgramRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;

class ServiceResource extends JsonResourceWithSchedules
{
    private Authenticatable $user;
    private Feedback|null $feedback;
    private Collection $activeUserPrograms;
    private Collection $activeUserAbonnements;

    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->user = auth('api')->user();
    }

    public function toArray(Request $request): array
    {
        $this->loadUserAssets();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'duration' => $this->duration,
            'places_count' => $this->places_count,
            'complexity' => $this->complexity,
            'price' => $this->price,
            'type' => $this->getType(),
            'has_active_user_program' => $this->activeUserPrograms->isNotEmpty() ? true : false,
            'has_active_user_abonnement' => $this->activeUserAbonnements->isNotEmpty() ? true : false,
            'my_feedback' => new FeedbackResource($this->whenLoaded('myFeedback')),
            'photos' => FileResource::collection($this->whenLoaded('photos')),
            'instructors' => InstructorResource::collection($this->whenLoaded('instructors')),
            'schedules' => ServiceScheduleResource::collection($this->whenLoaded('activeSchedules')),
            'profitable_programs' => ProgramResource::collection($this->whenLoaded('profitablePrograms')),
        ];
    }

    private function loadUserAssets(): void
    {
        $this->activeUserPrograms = new Collection();
        $this->activeUserAbonnements = new Collection();

        if ($this->resource->relationLoaded('activeSchedules')) {
            $this->activeUserPrograms = (new UserProgramRepository())
                ->getWhereAvailableForService($this->resource, $this->user);
            
            $this->activeUserAbonnements = (new UserAbonnementRepository())
                ->getWhereAvailableForService($this->resource, $this->user);
        }
    }
}
