<?php

namespace App\Models;

use App\Models\Traits\HasPhotosAttribute;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserAbonnement extends Model
{
    use HasFactory, HasPhotosAttribute;

    const STATUS_ACTIVE = 1;
    const STATUS_NON_ACTIVE = 2;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'expiration_date',
        'minutes',
        'status',

        'abonnement_id',
        'old_title',
        'old_duration_in_days',
        'old_minutes',
        'old_price',
        'photos',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function abonnement(): BelongsTo
    {
        return $this->belongsTo(Abonnement::class, 'abonnement_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function presents(): HasMany
    {
        return $this->hasMany(UserAbonnementPresent::class, 'user_abonnement_id');
    }

    protected function visitsByMinutes(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                return [
                    '30_min' => floor($this->minutes / 30),
                    '45_min' => floor($this->minutes / 45),
                    '55_min' => floor($this->minutes / 55),
                    '90_min' => floor($this->minutes / 90),
                ];
            }
        );
    }

    protected function oldVisitsByMinutes(): Attribute
    {
        return Attribute::make(
            get: function () {
                return [
                    '30_min' => floor($this->old_minutes / 30),
                    '45_min' => floor($this->old_minutes / 45),
                    '55_min' => floor($this->old_minutes / 55),
                    '90_min' => floor($this->old_minutes / 90),
                ];
            }
        );
    }

    public static function getStatusText(int $status): string
    {
        return match ($status) {
            self::STATUS_ACTIVE => 'Активен',
            self::STATUS_NON_ACTIVE => 'Неактивен',
        };
    }
}
