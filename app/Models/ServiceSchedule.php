<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceSchedule extends Model
{
    use HasFactory;

    const HALL_LIGHT = 1;
    const HALL_DARK = 2;

    protected $fillable = [
        'hall',
        'service_id',
        'start_date',
        'start_time',
        'places_count_total',
        'places_count_left',
        'complexity',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(UserServiceSchedule::class, 'service_schedule_id');
    }

    public function myFeedback(): HasOne
    {
        return $this->hasOne(Feedback::class, 'schedule_id', 'id')
            ->where('user_id', auth('api')->user()->id);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class, 'schedule_id');
    }

    protected function startDateTime(): Attribute
    {
        return Attribute::make(
            get: function () {
                return \Carbon\Carbon::parse($this->start_date . ' ' . $this->start_time);
            }
        );
    }

    public function getHall(): string
    {
        if (!isset($this->hall)) {
            return '';
        }

        return match ($this->hall) {
            self::HALL_LIGHT => 'Светлый зал',
            self::HALL_DARK => 'Темный зал',
        };
    }

    public static function getHalls(): array
    {
        return [
            self::HALL_LIGHT => 'Светлый зал',
            self::HALL_DARK => 'Темный зал',
        ];
    }
}
