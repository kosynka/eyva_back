<?php

namespace App\Repositories;

use App\Models\Program;
use App\Models\Service;
use App\Models\User;
use App\Models\UserProgram;
use App\Models\UserProgramService;
use App\Repositories\Contracts\Repository;
use Illuminate\Database\Eloquent\Collection;

class UserProgramRepository implements Repository
{
    public function create(Program $program, User $user): UserProgram
    {
        $userProgram = UserProgram::create([
            'user_id' => $user->id,
            'expiration_date' => now()->addDays($program->duration_in_days)->toDateString(),
            'status' => UserProgram::STATUS_ACTIVE,
            'program_id' => $program->id,
            'old_title' => $program->title,
            'old_description' => $program->description,
            'old_requirements' => $program->requirements,
            'old_duration_in_days' => $program->duration_in_days,
            'old_price' => $program->price,
            'photos' => $program->photos()->get(['type', 'link', 'preview'])->toArray(),
        ]);

        foreach ($program->programServices as $programService) {
            UserProgramService::create([
                'user_program_id' => $userProgram->id,
                'service_id' => $programService->service_id,
                'visits' => $programService->visits,
                'program_service_id' => $programService->id,
                'old_visits' => $programService->visits,
            ]);
        }

        return $userProgram;
    }

    public function getWhereAvailableForService(Service $service, User $user): Collection
    {
        return UserProgram::where('user_id', $user->id)
            ->where('expiration_date', '>=', now()->toDateTimeString())
            ->whereHas('programServices', function ($query) use ($service) {
                $query->where('visits', '>', 0)
                    ->whereHas('programService', function ($query) use ($service) {
                        $query->where('service_id', $service->id);
                    });
            })
            ->get();
    }
}
