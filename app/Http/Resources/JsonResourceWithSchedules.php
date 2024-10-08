<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class JsonResourceWithSchedules extends JsonResource
{
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }

    protected function groupSchedules($schedules): Collection
    {
        return $schedules->groupBy([
            function ($schedule) {
                return Carbon::parse($schedule->start_date)->format('Y-m-d'); 
            },
            function ($schedule) {
                return Carbon::parse($schedule->start_time)->format('H:i'); 
            }
        ]);
    }
}
