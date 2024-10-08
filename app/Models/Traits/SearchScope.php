<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait SearchScope
{
    public function scopeSearch(Builder $query, string $search = null): Builder
    {
        if ($search === null) {
            return $query;
        }

        $keywords = array_map('trim', explode(' ', $search));

        $query->where(function ($query) use ($keywords) {
            foreach ($keywords as $keyword) {
                $query->orWhereRaw("title ilike '%$keyword%'")
                    ->orWhereRaw("description ilike '%$keyword%'")
                    ->orWhereRaw("requirements ilike '%$keyword%'")
                    ->orWhereRaw('searchtext @@ plainto_tsquery(\'russian\', ?)', [$keyword]);
            }
        });

        return $query->orderByRaw('ts_rank(searchtext, plainto_tsquery(\'russian\', ?)) DESC', [$search]);
    }
}
