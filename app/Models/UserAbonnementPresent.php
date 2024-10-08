<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAbonnementPresent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_abonnement_id',
        'visits',

        'abonnement_present_id',
        'old_text',
        'old_visits',
        'service_id',
    ];

    public function userAbonnement(): BelongsTo
    {
        return $this->belongsTo(UserAbonnement::class, 'user_abonnement_id');
    }

    public function abonnementPresent(): BelongsTo
    {
        return $this->belongsTo(AbonnementPresent::class, 'abonnement_present_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
