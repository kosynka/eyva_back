<?php

namespace App\Models;

use App\Models\Traits\SearchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Service extends Model
{
    use HasFactory, SearchScope;

    const TYPE_SERVICE = 1;
    const TYPE_GROUP = 2;
    const TYPE_INDIVIDUAL = 3;
    const TYPE_MASTERCLASS = 4;
    const TYPE_MOVIE_NIGHT = 5;

    protected $with = ['photos', 'instructors'];

    protected $fillable = [
        'type',
        'title',
        'description',
        'requirements',
        'duration',
        'places_count',
        'complexity',
        'price',
    ];

    public function instructorServices(): HasMany
    {
        return $this->hasMany(InstructorService::class, 'service_id');
    }

    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'instructor_services');
    }

    public function myFeedback(): HasOne
    {
        return $this->hasOne(Feedback::class, 'service_id', 'id')
            ->where('user_id', auth('api')->user()->id);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class, 'service_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ServiceSchedule::class, 'service_id');
    }

    public function activeSchedules(): HasMany
    {
        return $this->schedules()
            ->whereRaw('(start_date + start_time::interval) >= ?', [now()->addHours(1)->toDateTimeString()])
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc');
    }

    public function profitablePrograms(): HasManyThrough
    {
        return $this->hasManyThrough(
            Program::class,
            ProgramService::class,
            'service_id',
            'id',
            'id',
            'program_id',
        )
        ->isEnabled()
        ->distinct('id');
    }

    public function programServices(): HasMany
    {
        return $this->hasMany(ProgramService::class, 'service_id');
    }

    public function categoryServices(): HasMany
    {
        return $this->hasMany(CategoryService::class, 'service_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_services');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ServicePhoto::class, 'service_id');
    }

    public function getType(bool $asText = false): string
    {
        if ($asText) {
            return self::getTypeText($this->type);
        }

        return match ($this->type) {
            self::TYPE_SERVICE => 'service',
            self::TYPE_GROUP => 'group',
            self::TYPE_INDIVIDUAL => 'individual',
            self::TYPE_MASTERCLASS => 'masterclass',
            self::TYPE_MOVIE_NIGHT => 'movie_night',
            default => null,
        };
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_SERVICE => 'Услуга',
            self::TYPE_GROUP => 'Групповое занятие',
            self::TYPE_INDIVIDUAL => 'Индивидуальное занятие',
            self::TYPE_MASTERCLASS => 'Мастер-класс',
            self::TYPE_MOVIE_NIGHT => 'Киновечер',
        ];
    }

    public static function getTypeText(int $type): string
    {
        return match ($type) {
            self::TYPE_SERVICE => 'Услуга',
            self::TYPE_GROUP => 'Групповое занятие',
            self::TYPE_INDIVIDUAL => 'Индивидуальное занятие',
            self::TYPE_MASTERCLASS => 'Мастер-класс',
            self::TYPE_MOVIE_NIGHT => 'Киновечер',
            default => null,
        };
    }
}
