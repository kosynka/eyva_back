<?php

namespace App\Services;

use App\Helpers\AmountConverter;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Contracts\Acquiring;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcquiringService
{
    use AmountConverter;

    public const DEFAULT_CURRENCY = 'KZT';

    public function __construct(
        private Acquiring $acquiring
    ) {
    }

    public function createPayment(User $user, int $amount): array
    {
        $response = [];

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'type' => Transaction::TYPE_REPLENISHMENT,
                'amount' => $amount,
                'amount_in_currency' => $this->convertEyvToKzt($amount),
                'currency' => self::DEFAULT_CURRENCY,
                'status' => Transaction::STATUS_STARTED,
            ]);

            $response = $this->acquiring
                ->setAmount($this->convertEyvToKzt($amount))
                ->initializePayment($user, $transaction->id);

            $transaction->payment_service_id = $response['id'] ?? null;
            $transaction->status = $response['status'] !== 200
                ? Transaction::STATUS_FAILED
                : $transaction->status;

            $transaction->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            $response['message'] = $e->getMessage();

            Log::error($e->getMessage(), $e->getTrace());
        }

        return $response;
    }

    public function setPaymentStatus(array $data): array
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::with(['user'])
                ->where([
                    'id' => $data['orderId'],
                    'payment_service_id' => $data['id'],
                ])
                ->first();

            if (!$transaction) {
                return [
                    'accepted' => false,
                ];
            }

            $checkedData = $this->acquiring->getStatus($data);

            $this->addBalance($transaction, $checkedData);

            $transaction->status = $checkedData['status'] === 200
                ? Transaction::STATUS_SUCCESS
                : Transaction::STATUS_FAILED;

            $transaction->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage(), $e->getTrace());

            return response()->json([
                'accepted' => false,
            ], 500);
        }

        return [
            'accepted' => $checkedData['status'] === 200 ? true : false,
        ];
    }

    private function addBalance(Transaction $transaction, array $checkedData): void
    {
        if ($checkedData['status'] !== 200) {
            $transaction->status = Transaction::STATUS_FAILED;
            $transaction->save();

            return;
        }

        if ($transaction->status !== Transaction::STATUS_SUCCESS) {
            $transaction->user->balance += $transaction->amount;
            $transaction->user->save();
        }

        $transaction->status = Transaction::STATUS_SUCCESS;
        $transaction->save();
    }
}
