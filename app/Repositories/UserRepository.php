<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Models\User;
use App\Repositories\Contracts\Repository;

class UserRepository implements Repository
{
    public function replenishBalance(User $user, array $data): Transaction
    {
        $user->balance += $data['amount'];
        $user->save();

        if (isset($data['transaction_id'])) {
            $transaction = Transaction::where([
                'id' => $data['transaction_id'],
                'user_id' => $user->id,
            ])->firstOrFail();

            $transaction->status = Transaction::STATUS_SUCCESS;
            $transaction->save();

            return $transaction;
        }

        return Transaction::create([
            'user_id' => $user->id,
            'type' => Transaction::TYPE_REPLENISHMENT,
            'amount' => $data['amount'],
            'status' => Transaction::STATUS_SUCCESS,
            'related_with' => null,
            'related_id' => null,
            'comment' => $data['comment'] ?? null,
        ]);
    }
}
