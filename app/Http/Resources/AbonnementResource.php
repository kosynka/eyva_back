<?php

namespace App\Http\Resources;

use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbonnementResource extends JsonResource
{
    private ServiceRepository $serviceRepository;

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
                'services_count' => count($serviceList),
                'services' => ServiceResource::collection($serviceList),
            ];
        }

        return $servicesByMinutes;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'duration_in_days' => $this->duration_in_days,
            'price' => $this->price,
            'number_of_presents' => $this->presents_count ?? null,
            'photos' => FileResource::collection($this->whenLoaded('photos')),
            'presents' => AbonnementPresentResource::collection($this->whenLoaded('presents')),
            'services_by_minutes' => $this->getAllServicesByMinutes(),
        ];
    }
}
