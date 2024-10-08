<?php

namespace App\Models;

use App\Models\Traits\HasPhotosAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserProgram extends Model
{
    use HasFactory, HasPhotosAttribute;

    const STATUS_ACTIVE = 1;
    const STATUS_NON_ACTIVE = 2;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'expiration_date',
        'status',

        'program_id',
        'old_title',
        'old_description',
        // 'old_requirements',
        'old_duration_in_days',
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

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function programServices(): HasMany
    {
        return $this->hasMany(UserProgramService::class, 'user_program_id', 'id');
    }

    public function wasUpdated(): bool
    {
        $this->load(['program.programServices']);

        if (!isset($this->program)) {
            return true;
        }

        $servicesWasUpdated = $this->program->programServices
            ->where('updated_at', '>=', $this->updated_at)->isNotEmpty();

        if ($this->program->updated_at >= $this->updated_at || $servicesWasUpdated) {
            return true;
        }

        return false;
    }

    public static function getStatusText(int $status): string
    {
        return match ($status) {
            self::STATUS_ACTIVE => 'Активен',
            self::STATUS_NON_ACTIVE => 'Неактивен',
        };
    }
}
