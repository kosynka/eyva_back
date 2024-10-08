<?php

namespace App\Filament\Widgets;

use App\Repositories\StatsRepository;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsOverview extends BaseWidget
{
    private StatsRepository $statsRepository;

    public function __construct()
    {
        $this->statsRepository = new StatsRepository();
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Количество пользователей', $this->statsRepository->usersCount())
                ->icon('heroicon-o-users')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->description('+' . $this->statsRepository->usersTodayCount() . ' за сегодня')
                ->color('success'),
            Stat::make('Количество записей', $this->statsRepository->visitsCount())
                ->icon('heroicon-c-calendar-date-range')
                ->description('+' . $this->statsRepository->visitsTodayCount() . ' за сегодня')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Сумма пополнений', $this->statsRepository->revenue())
                ->icon('heroicon-o-currency-dollar')
                ->description('+' . $this->statsRepository->revenueToday() . ' за сегодня')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($this->statsRepository->revenueByDays())
                ->color('success'),
        ];
    }
}
