<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\Contracts\Repository;
use Illuminate\Database\Eloquent\Model;

class TransactionRepository implements Repository
{
    public function bindTransaction(Model $transactionableModel, Transaction $transaction): bool
    {
        $transaction->related_with = $transactionableModel->getTable();
        $transaction->related_id = $transactionableModel->id;
        $saveTransaction = $transaction->save();

        $transactionableModel->transaction_id = $transaction->id;
        $saveBindedModel = $transactionableModel->save();

        return $saveTransaction && $saveBindedModel;
    }
}
