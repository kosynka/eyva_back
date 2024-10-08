<?php

namespace App\Repositories;

use App\Models\ServiceSchedule;
use App\Models\User;
use App\Models\UserServiceSchedule;
use App\Repositories\Contracts\Repository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class UserServiceScheduleRepository implements Repository
{
    public function intersectingEnrollements(User $user, ServiceSchedule $schedule): Collection
    {
        $startDateTime = Carbon::parse("$schedule->start_date $schedule->start_time");
        $endDateTime = $startDateTime->addMinutes($schedule->service->duration);

        return $user->userServiceSchedules()
            ->with('schedule')
            ->where('status', UserServiceSchedule::STATUS_ENROLLED)
            ->whereHas('schedule', function ($query) use ($startDateTime, $endDateTime) {
                $query->whereRaw("(start_date + start_time::interval) >= ?", [$startDateTime->toDateTimeString()])
                    ->whereRaw("(start_date + start_time::interval) <= ?", [$endDateTime->toDateTimeString()]);
            })
            ->get();
    }

    public function hasIntersectingEnrollements(User $user, ServiceSchedule $schedule): bool
    {
        return $this->intersectingEnrollements($user, $schedule)->count() > 0;
    }
}
