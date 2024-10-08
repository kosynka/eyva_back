<?php

namespace App\Services\PaymentStrategy;

use App\Models\User;
use App\Models\UserAbonnement;
use App\Models\UserServiceSchedule;
use App\Services\Contracts\PaymentStrategy;
use Symfony\Component\HttpFoundation\Response;

class AbonnementPaymentStrategy implements PaymentStrategy
{
    public function handlePayment(array $data, User $user): int
    {
        $userAbonnement = UserAbonnement::findOrFail($data['user_abonnement_id']);

        if ($userAbonnement->user_id !== $user->id) {
            throw new \Exception(
                'Вы не можете записаться по этому абонементу',
                Response::HTTP_FORBIDDEN,
            );
        }

        if ($userAbonnement->minutes < $data['duration']) {
            throw new \Exception(
                'Вы не можете записаться по этому абонементу',
                Response::HTTP_NOT_ACCEPTABLE,
            );
        }

        $userAbonnement->minutes -= $data['duration'];
        $userAbonnement->save();

        return UserServiceSchedule::TYPE_ABONNEMENT;
    }
}
