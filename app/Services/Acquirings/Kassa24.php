<?php

namespace App\Services\Acquirings;

use App\Helpers\AmountConverter;
use App\Models\User;
use App\Services\Contracts\Acquiring;
use Illuminate\Support\Facades\Log;

class Kassa24 implements Acquiring
{
    use AmountConverter;

    private const STATUS_FAILED = 0; // Неуспешная транзакция
    private const STATUS_SUCCESS = 1; // Успешная транзакция
    private const STATUS_BLOCKED = 2; // Сумма успешно заблокирована (для двухэтапных транзакций)
    private const STATUS_CANCELED = 3; // Транзакция отменена или был совершен возврат

    public function __construct(
        private ?array $config = null,
        private ?string $returnUrl = null,
        private ?string $callbackUrl = null,
        private ?int $amount = null,
    ) {
        $this->config = config('payment.kassa24');
        $this->returnUrl = config('payment.url.return');
        $this->callbackUrl = config('payment.url.callback');
    }

    public function initializePayment(User $user, int $orderId): array
    {
        $data_string = json_encode([
            'merchantId' => strval($this->config['merchant_id']),
            'callbackUrl' => strval($this->callbackUrl),
            'orderId' => strval($orderId),
            'description' => strval(strval($user->id)),
            'demo' => env('APP_ENV') !== 'production',
            'returnUrl' => strval($this->returnUrl),
            'amount' => $this->getAmount(),
        ], JSON_UNESCAPED_UNICODE);

        $curl = curl_init("{$this->config['base_url']}/payment/create");
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->config['login'] . ':' . $this->config['password']),
            'Content-Length: ' . strlen($data_string),
        ]);

        $result = curl_exec($curl);
        curl_close($curl);

        if (!$result) {
            Log::error('Kassa24: Ошибка при инициализации платежа');

            return [
                'message' => 'Ошибка при инициализации платежа',
                'status' => 500,
                'data' => $result,
            ];
        }

        $data = (array) json_decode($result);

        return [
            'message' => 'Платеж успешно инициализирован',
            'status' => 200,
            'url' => $data['url'],
            'id' => $data['id'],
        ];
    }

    public function getStatus(array $data): array
    {
        if ($this->statusFailed($data['status'])) {
            Log::error('Kassa24: Ошибка при инициализации платежа', $data);

            return [
                'message' => $data['errMessage'],
                'status' => 500,
                'response' => $data,
            ];
        }

        return [
            'message' => $data['errMessage'],
            'status' => 200,
            'response' => $data,
        ];
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    protected function getAmount(): ?int
    {
        if ($this->amount === null || $this->amount <= 0) {
            throw new \Exception('Цена не указана или меньше 0');
        }

        return $this->convertKztToKztCoin($this->amount);
    }

    private function getAuthorizationToken(): string
    {
        if (! isset($this->config['login'], $this->config['password'])) {
            throw new \Exception('Не указаны логин и пароль для Kassa24');
        }

        return 'Basic ' . base64_encode("{$this->config['login']}:{$this->config['password']}");
    }

    private function statusFailed(int $status): bool
    {
        return $status !== self::STATUS_SUCCESS;
    }
}
