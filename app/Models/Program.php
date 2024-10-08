<?php

namespace App\Models;

use App\Models\Traits\IsEnabledScope;
use App\Models\Traits\SearchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Program extends Model
{
    use HasFactory, SearchScope, IsEnabledScope;

    protected $fillable = [
        'title',
        'description',
        // 'requirements',
        'duration_in_days',
        'price',
        'status',
    ];

    public function programServices(): HasMany
    {
        return $this->hasMany(ProgramService::class, 'program_id');
    }

    public function userPrograms(): HasMany
    {
        return $this->hasMany(UserProgram::class, 'program_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ProgramPhoto::class, 'program_id');
    }

    public function myFeedback(): HasOne
    {
        return $this->hasOne(Feedback::class, 'service_id')
            ->where('user_id', auth('api')->user()->id);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class, 'service_id');
    }
}
