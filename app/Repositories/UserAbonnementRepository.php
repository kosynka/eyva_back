<?php

namespace App\Repositories;

use App\Models\Abonnement;
use App\Models\Service;
use App\Models\User;
use App\Models\UserAbonnement;
use App\Models\UserAbonnementPresent;
use App\Repositories\Contracts\Repository;
use Illuminate\Database\Eloquent\Collection;

class UserAbonnementRepository implements Repository
{
    public function create(Abonnement $abonnement, User $user): UserAbonnement
    {
        $userAbonnement = UserAbonnement::create([
            'user_id' => $user->id,
            'expiration_date' => now()->addDays($abonnement->duration_in_days)->toDateString(),
            'minutes' => $abonnement->minutes,
            'status' => UserAbonnement::STATUS_ACTIVE,
            'abonnement_id' => $abonnement->id,
            'old_title' => $abonnement->title,
            'old_description' => $abonnement->description,
            'old_duration_in_days' => $abonnement->duration_in_days,
            'old_minutes' => $abonnement->minutes,
            'old_price' => $abonnement->price,
            'photos' => $abonnement->photos()->get(['type', 'link', 'preview'])->toArray(),
        ]);

        foreach ($abonnement->presents as $present) {
            UserAbonnementPresent::create([
                'user_abonnement_id' => $userAbonnement->id,
                'visits' => $present->visits,
                'abonnement_present_id' => $present->id,
                'old_text' => $present->text,
                'old_visits' => $present->visits,
                'service_id' => $present->service_id,
            ]);
        }

        return $userAbonnement;
    }

    public function getWhereAvailableForService(Service $service, User $user): Collection
    {
        return UserAbonnement::where('user_id', $user->id)
            ->where('expiration_date', '>=', now()->toDateTimeString())
            ->where(function ($query) use ($service) {
                $query->where('minutes', '>=', $service->duration)
                    ->orWhereHas('presents', function ($query) use ($service) {
                        $query->whereHas('abonnementPresent', function ($query) use ($service) {
                            $query->where('service_id', $service->id);
                        })->where('visits', '>', 0);
                    });
                })
            ->get();
    }
}
