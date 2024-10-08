<?php

namespace App\Models\Traits;

use App\Enums\BuyableStatusEnum;
use Illuminate\Database\Eloquent\Builder;

trait IsEnabledScope
{
    public function scopeIsEnabled(Builder $query, bool $enabled = true): Builder
    {
        $enabledStatus = $enabled ? BuyableStatusEnum::ENABLED_KEY : BuyableStatusEnum::DISABLED_KEY;

        return $query->where('status', $enabledStatus);
    }
}
