<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProgramService extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_program_id',
        'service_id',
        'visits',

        'program_service_id',
        'old_visits',
    ];

    public function userProgram(): BelongsTo
    {
        return $this->belongsTo(UserProgram::class, 'user_program_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function programService(): BelongsTo
    {
        return $this->belongsTo(ProgramService::class, 'program_service_id');
    }
}
