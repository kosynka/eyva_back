<?php

namespace App\Models;

use App\Models\Traits\IsEnabledScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Abonnement extends Model
{
    use HasFactory, IsEnabledScope;

    protected $fillable = [
        'title',
        'duration_in_days',
        'minutes',
        'price',
        'status',
    ];

    public function presents(): HasMany
    {
        return $this->hasMany(AbonnementPresent::class, 'abonnement_id');
    }

    public function userAbonnements(): HasMany
    {
        return $this->hasMany(UserAbonnement::class, 'abonnement_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(AbonnementPhoto::class, 'abonnement_id');
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
}
