<?php

namespace App\Http\Resources;

use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAbonnementResource extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->serviceRepository = new ServiceRepository();
    }

    private function getAllServicesByMinutes(): array
    {
        $servicesByMinutes = [];

        foreach ($this->serviceRepository->getAllGroupedByDuration() as $minut => $serviceList) {
            $key = "{$minut}_min";

            $servicesByMinutes[$key] = [
                'visits' => floor($this->minutes / $minut),
                'total_visits' => floor($this->old_minutes / $minut),
                'services_count' => count($serviceList),
                'services' => ServiceResource::collection($serviceList),
            ];
        }

        return $servicesByMinutes;
    }

    public function toArray(Request $request): array
    {
        $daysUntilExpiration = ceil(now()->diffInDays($this->expiration_date));

        return [
            'id' => $this->id,
            'title' => $this->old_title,
            'duration_in_days' => $this->old_duration_in_days,
            'expiration_date' => $this->expiration_date,
            'days_until_expiration' => $daysUntilExpiration,

            'description' => $this->old_description,
            'price' => $this->old_price,
            'number_of_presents' => $this->presents_count ?? null,

            'photos' => $this->photosObject,
            'presents' => UserAbonnementPresentResource::collection($this->whenLoaded('presents')),
            'services_by_minutes' => $this->getAllServicesByMinutes(),
            'abonnement' => new AbonnementResource($this->whenLoaded('abonnement')),
        ];
    }
}
