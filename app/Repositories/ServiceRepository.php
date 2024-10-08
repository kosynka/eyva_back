<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Contracts\Repository;
use Illuminate\Database\Eloquent\Collection;

class ServiceRepository implements Repository
{
    public function getAllGroupedByDuration(array $with = []): Collection
    {
        return Service::with($with)
            ->orderBy('duration')
            ->get()
            ->groupBy('duration');
    }
}
