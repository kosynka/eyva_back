<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserServiceSchedule extends Model
{
    use HasFactory;

    const TYPE_PRIMARY = 1;
    const TYPE_PROGRAM = 2;
    const TYPE_ABONNEMENT = 3;

    const STATUS_ENROLLED = 0;
    const STATUS_FINISHED = 1;
    const STATUS_SKIPPED = 2;

    protected $fillable = [
        'type',
        'user_id',
        'service_schedule_id',
        'transaction_id', // TYPE_PRIMARY
        'user_program_service_id', // TYPE_PROGRAM
        'user_abonnement_id', // TYPE_ABONNEMENT
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ServiceSchedule::class, 'service_schedule_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function userAbonnement(): BelongsTo
    {
        return $this->belongsTo(UserAbonnement::class, 'user_abonnement_id');
    }

    public function userProgramService(): BelongsTo
    {
        return $this->belongsTo(UserProgramService::class, 'user_program_service_id');
    }

    public function getStatus(): string
    {
        return match ($this->status) {
            self::STATUS_ENROLLED => 'Зачислен',
            self::STATUS_FINISHED => 'Завершено',
            self::STATUS_SKIPPED => 'Пропущен',
        };
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ENROLLED => 'Зачислен',
            self::STATUS_FINISHED => 'Завершено',
            self::STATUS_SKIPPED => 'Пропущен',
        ];
    }

    public function getType(): string
    {
        return match ($this->type) {
            self::TYPE_PROGRAM => 'Программа',
            self::TYPE_ABONNEMENT => 'Абонемент',
            self::TYPE_PRIMARY => 'Баланс',
        };
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_PROGRAM => 'Программа',
            self::TYPE_ABONNEMENT => 'Абонемент',
            self::TYPE_PRIMARY => 'Баланс',
        ];
    }
}
