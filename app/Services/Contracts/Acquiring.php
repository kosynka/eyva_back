<?php

namespace App\Services\Contracts;

use App\Models\User;

/**
 * Интерфейс для работы с платежными системами
 */
interface Acquiring
{
    /**
     * Инициализация платежа
     * 
     * @param User $user - пользователь
     * @param int $orderId - идентификатор платежа(transaction id)
     * 
     * @throws \Exception
     * @return array{status: int, message: string, url: string} $responseData
     */
    public function initializePayment(User $user, int $orderId): array;

    /**
     * Получение статуса платежа
     * 
     * @param array $data - данные платежа
     * 
     * @return array{status: int, message: string, response: array} $responseData
     * @throws \Exception
     */
    public function getStatus(array $data): array;

    /**
     * Установка суммы платежа
     * 
     * @param int $amount - сумма платежа больше 0
     * 
     * @throws \Exception
     * @return static
     */
    public function setAmount(int $amount): static;
}
