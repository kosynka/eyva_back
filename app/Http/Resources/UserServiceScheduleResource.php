<?php

namespace App\Http\Resources;

use App\Repositories\PageElementRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserServiceScheduleResource extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->pageElementRepository = new PageElementRepository();
    }

    public function toArray(Request $request): array
    {
        return [
            'enrollment_id' => $this->id,
            'status' => $this->getStatus(),
            'address' => new PageElementResource($this->pageElementRepository->getAddress()),
            'schedule' => new ServiceScheduleResource($this->whenLoaded('schedule')),
        ];
    }
}
