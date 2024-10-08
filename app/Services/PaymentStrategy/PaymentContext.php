<?php

namespace App\Services\PaymentStrategy;

use App\Models\Transaction;
use App\Models\User;
use App\Repositories\TransactionRepository;
use App\Services\Contracts\PaymentStrategy;
use Illuminate\Database\Eloquent\Model;

class PaymentContext
{
    private PaymentStrategy $paymentStrategy;
    private TransactionRepository $transactionRepository;

    public function __construct(private array $data, private User $user)
    {
        $this->transactionRepository = new TransactionRepository();
    }

    public function setPaymentStrategy(string $type = 'balance'): void
    {
        switch ($type) {
            case 'balance':
                $this->paymentStrategy = new BalancePaymentStrategy();
                break;
            case 'program':
                $this->paymentStrategy = new ProgramPaymentStrategy();
                break;
            case 'abonnement':
                $this->paymentStrategy = new AbonnementPaymentStrategy();
                break;
            case 'abonnement_present':
                $this->paymentStrategy = new AbonnementPresentPaymentStrategy();
                break;
            default:
                $this->paymentStrategy = new BalancePaymentStrategy();
        }
    }

    public function executeStrategy(): int
    {
        return $this->paymentStrategy->handlePayment($this->data, $this->user);
    }

    public function bindTransaction(Model $transactionableModel, Transaction $transaction): bool
    {
        return $this->transactionRepository->bindTransaction($transactionableModel, $transaction);
    }
}
