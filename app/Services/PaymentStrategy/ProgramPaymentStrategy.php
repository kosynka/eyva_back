<?php

namespace App\Services\PaymentStrategy;

use App\Models\User;
use App\Models\UserProgram;
use App\Models\UserServiceSchedule;
use App\Services\Contracts\PaymentStrategy;
use Symfony\Component\HttpFoundation\Response;

class ProgramPaymentStrategy implements PaymentStrategy
{
    public function handlePayment(array $data, User $user): int
    {
        $userProgram = UserProgram::with(['programServices.service'])
            ->findOrFail($data['user_program_id']);

        if ($userProgram->user_id !== $user->id) {
            throw new \Exception(
                'Вы не можете записаться по этой программе',
                Response::HTTP_FORBIDDEN,
            );
        }

        $programService = $userProgram->programServices()
            ->where('service_id', $data['service_id'])
            ->first();

        if (!isset($programService) || $programService->visits < 1) {
            throw new \Exception(
                'Недостаточно визитов для этой услуги по этой программе',
                Response::HTTP_NOT_ACCEPTABLE,
            );
        }

        $programService->visits -= 1;
        $programService->save();

        return UserServiceSchedule::TYPE_PROGRAM;
    }
}
