<?php

namespace App\Models;

use App\Enums\FileEnum;
use App\Models\Contracts\ModelWithFileObject;
use App\Models\Traits\HasFileObject;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

#[\Illuminate\Database\Eloquent\Attributes\ObservedBy([\App\Observers\FileObjectObserver::class])]
class User extends Authenticatable implements JWTSubject, FilamentUser, ModelWithFileObject
{
    use HasFactory, Notifiable, HasFileObject;

    const ROLE_ADMIN = 1;
    const ROLE_EMPLOYEE = 2;
    const ROLE_USER = 3;

    protected $fillable = [
        'name',
        'employee_description',
        'email',
        'phone',
        'photo',
        'birth_date',
        'role',
        'balance',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'float',
        ];
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '@eyva.kz');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id')
            ->orderBy('created_at', 'desc');
    }

    public function instructorServices(): HasMany
    {
        return $this->hasMany(InstructorService::class, 'user_id');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'instructor_services');
    }

    public function userAbonnements(): HasMany
    {
        return $this->hasMany(UserAbonnement::class, 'user_id');
    }

    public function abonnements(): BelongsToMany
    {
        return $this->belongsToMany(Abonnement::class, 'user_abonnements');
    }

    public function userPrograms(): HasMany
    {
        return $this->hasMany(UserProgram::class, 'user_id');
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'user_programs');
    }

    public function userServiceSchedules(): HasMany
    {
        return $this->hasMany(UserServiceSchedule::class, 'user_id');
    }

    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(ServiceSchedule::class, 'user_service_schedules');
    }

    public function fileTypeAttributeName(): FileEnum
    {
        return FileEnum::PHOTO_KEY;
    }

    public function fileLinkAttributeName(): string
    {
        return 'photo';
    }

    public function needPreview(): bool
    {
        return false;
    }
}
