<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserServiceSchedule;
use App\Repositories\Contracts\Repository;

class StatsRepository implements Repository
{
    public function usersCount(): int
    {
        return User::where([
                'role' => User::ROLE_USER,
            ])
            ->count();
    }

    public function usersTodayCount(): int
    {
        return User::where(['role' => User::ROLE_USER])
            ->whereDate('created_at', '=', now()->format('Y-m-d'))
            ->count();
    }

    public function visitsCount(): int
    {
        return UserServiceSchedule::get()
            ->count();
    }

    public function visitsTodayCount(): int
    {
        return UserServiceSchedule::whereDate('created_at', '=', now()->format('Y-m-d'))
            ->count();
    }

    public function revenue(): string
    {
        $sum = Transaction::where([
                'type' => Transaction::TYPE_REPLENISHMENT,
                'status' => Transaction::STATUS_SUCCESS,
            ])
            ->sum('amount');

        return number_format($sum, 0, '', ' ') . ' eyv';
    }

    public function revenueToday(): string
    {
        $sum = Transaction::where([
                'type' => Transaction::TYPE_REPLENISHMENT,
                'status' => Transaction::STATUS_SUCCESS,
            ])
            ->whereDate('created_at', '=', now()->format('Y-m-d'))
            ->sum('amount');

        return number_format($sum, 0, '', ' ') . ' eyv';
    }

    public function revenueByDays(): array
    {
        return Transaction::where([
                'type' => Transaction::TYPE_REPLENISHMENT,
                'status' => Transaction::STATUS_SUCCESS,
            ])
            ->selectRaw('DATE(created_at) as date, sum(amount) as sum')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($item) => $item['sum'])
            ->toArray();
    }
}
