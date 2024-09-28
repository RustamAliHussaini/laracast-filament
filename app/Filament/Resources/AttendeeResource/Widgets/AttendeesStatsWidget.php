<?php

namespace App\Filament\Resources\AttendeeResource\Widgets;

use App\Models\Attendee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendeesStatsWidget extends BaseWidget
{
    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Attendees Count', Attendee::count())
            ->description('Total Attendees Count')
            ->descriptionIcon('heroicon-o-user-group')
            ->color('success')
            ->chart([1,2,3,4,5,6,4,3,2,2,1,7,9]),
            Stat::make('Total Revenue', Attendee::sum('ticket_cost'))
            ->description('Total Revenue out of attendees')
            ->descriptionIcon('heroicon-o-user-group')
            ,


        ];
    }
}
