<?php

namespace App\Filament\Widgets;

use App\Models\Course;
use App\Models\Payment;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::query()->count())
                ->description('Registered app users'),
            Stat::make('Courses', Course::query()->count())
                ->description('Published learning products'),
            Stat::make('Revenue', 'INR '.number_format((float) Payment::query()->where('status', 'completed')->sum('amount'), 2))
                ->description('Verified premium sales'),
        ];
    }
}
