<?php

namespace App\Services\PaymentStrategy;

use App\Models\User;
use App\Models\UserAbonnementPresent;
use App\Models\UserServiceSchedule;
use App\Services\Contracts\PaymentStrategy;
use Symfony\Component\HttpFoundation\Response;

class AbonnementPresentPaymentStrategy implements PaymentStrategy
{
    public function handlePayment(array $data, User $user): int
    {
        $userAbonnementPresent = UserAbonnementPresent::with(['userAbonnement'])
            ->findOrFail($data['user_abonnement_present_id']);

        if ($userAbonnementPresent->userAbonnement->user_id !== $user->id) {
            throw new \Exception(
                'Вы не можете записаться по этому подарку',
                Response::HTTP_FORBIDDEN,
            );
        }

        if ($userAbonnementPresent->visits < 1) {
            throw new \Exception(
                'Недостаточно визитов для этой услуги по этому подарку',
                Response::HTTP_NOT_ACCEPTABLE,
            );
        }

        $userAbonnementPresent->visits -= 1;
        $userAbonnementPresent->save();

        return UserServiceSchedule::TYPE_ABONNEMENT;
    }
}
