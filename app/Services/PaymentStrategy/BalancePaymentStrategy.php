<?php

namespace App\Services\PaymentStrategy;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserServiceSchedule;
use App\Services\Contracts\PaymentStrategy;
use Symfony\Component\HttpFoundation\Response;

class BalancePaymentStrategy implements PaymentStrategy
{
    public function handlePayment(array $data, User $user): int
    {
        if ($user->balance < $data['price']) {
            throw new \Exception(
                'Недостаточно средств',
                Response::HTTP_NOT_ACCEPTABLE,
            );
        }

        $user->balance -= $data['price'];
        $user->save();

        Transaction::create([
            'user_id' => $user->id,
            'type' => $data['transaction_type'],
            'amount' => $data['price'],
            'status' => Transaction::STATUS_SUCCESS,
        ]);

        return UserServiceSchedule::TYPE_PRIMARY;
    }
}
