<?php

namespace App\Repositories;

use App\Models\ServiceSchedule;
use App\Models\User;
use App\Models\UserServiceSchedule;
use App\Repositories\Contracts\Repository;

class UserServiceRepository implements Repository
{
    public function enroll(ServiceSchedule $schedule, User $user, int $type): UserServiceSchedule
    {
        $schedule->places_count_left -= 1;
        $schedule->save();

        return UserServiceSchedule::create([
            'type' => $type,
            'user_id' => $user->id,
            'service_schedule_id' => $schedule->id,
            'status' => UserServiceSchedule::STATUS_ENROLLED,
        ]);
    }
}
