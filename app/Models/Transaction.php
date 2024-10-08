<?php

namespace App\Models;

use App\Helpers\ModelHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    const TYPE_REPLENISHMENT = 1;
    const TYPE_PURCHASE_PROGRAM = 2;
    const TYPE_PURCHASE_ABONNEMENT = 3;
    const TYPE_PURCHASE_SERVICE_SERVICE = 4;
    const TYPE_PURCHASE_SERVICE_GROUP = 5;
    const TYPE_PURCHASE_SERVICE_INDIVIDUAL = 6;
    const TYPE_PURCHASE_SERVICE_MASTERCLASS = 7;
    const TYPE_PURCHASE_SERVICE_MOVIE_NIGHT = 8;

    const STATUS_STARTED = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_FAILED = 3;

    protected $fillable = [
        'user_id',
        'payment_service_id',
        'type',
        'amount',
        'amount_in_currency',
        'currency',
        'status',
        'related_with',
        'related_id',
        'comment',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function related(): Attribute
    {
        return Attribute::make(
            get: function () {
                $model = ModelHelper::getModelClassFromTable($this->related_with);

                if (! $model) {
                    return null;
                }

                return $model::find($this->related_id);
            }
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getType(): string
    {
        return match ($this->type) {
            self::TYPE_REPLENISHMENT => 'Пополнение',
            self::TYPE_PURCHASE_PROGRAM => 'Покупка программы',
            self::TYPE_PURCHASE_ABONNEMENT => 'Покупка абонемента',
            self::TYPE_PURCHASE_SERVICE_SERVICE => 'Оплата услуги',
            self::TYPE_PURCHASE_SERVICE_GROUP => 'Оплата группового занятия',
            self::TYPE_PURCHASE_SERVICE_INDIVIDUAL => 'Оплата индивидуального занятия',
            self::TYPE_PURCHASE_SERVICE_MASTERCLASS => 'Оплата мастер-класса',
            self::TYPE_PURCHASE_SERVICE_MOVIE_NIGHT => 'Оплата киновечера',
        };
    }

    public function getStatus(): string
    {
        return match ($this->status) {
            self::STATUS_STARTED => 'Начата',
            self::STATUS_SUCCESS => 'Успешна',
            self::STATUS_FAILED => 'Провалена',
        };
    }

    public function getAmount(): string
    {
        $amount = number_format($this->amount, 0, ' ', ' ');

        return match ($this->type) {
            self::TYPE_REPLENISHMENT => "+$amount",
            default => "-$amount"
        };
    }

    public static function getServicePurchaseType(int $type): int
    {
        return match ($type) {
            Service::TYPE_SERVICE => self::TYPE_PURCHASE_SERVICE_SERVICE,
            Service::TYPE_GROUP => self::TYPE_PURCHASE_SERVICE_GROUP,
            Service::TYPE_INDIVIDUAL => self::TYPE_PURCHASE_SERVICE_INDIVIDUAL,
            Service::TYPE_MASTERCLASS => self::TYPE_PURCHASE_SERVICE_MASTERCLASS,
            Service::TYPE_MOVIE_NIGHT => self::TYPE_PURCHASE_SERVICE_MOVIE_NIGHT,
            default => null,
        };
    }
}
