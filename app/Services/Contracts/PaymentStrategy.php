<?php

namespace App\Services\Contracts;

use App\Models\User;

interface PaymentStrategy
{
    public function handlePayment(array $data, User $user): int;
}
