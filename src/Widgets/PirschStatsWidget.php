<?php

namespace Devlogx\FilamentPirsch\Widgets;

use Carbon\Carbon;
use Devlogx\FilamentPirsch\Concerns\Filter;
use Devlogx\FilamentPirsch\Facades\FilamentPirsch;
use Devlogx\FilamentPirsch\FilamentPirschPlugin;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Str;

class PirschStatsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -9;

    public function getPollingInterval(): ?string
    {
        return FilamentPirschPlugin::get()->getPollingInterval();
    }

    protected function getStats(): array
    {

        $dateParts = explode('-', Str::remove(' ', $this->filters['date_range']));
        $startDate = ! is_null($dateParts[0] ?? null) ?
            Carbon::parse($dateParts[0])->startOfDay() :
            now()->subDays(30)->startOfDay();

        $endDate = ! is_null($dateParts[1] ?? null) ?
            Carbon::parse($dateParts[1])->endOfDay() :
            now()->endOfDay();

        $diffInDays = round($startDate->diffInDays($endDate), 0);
        $myFilter = (new Filter())
            ->setFrom($startDate)
            ->setTo($endDate);

        [$total_visits, $visit_chart] = FilamentPirsch::visitors($myFilter);
        [$total_views, $views_chart] = FilamentPirsch::views($myFilter);

        return [
            Stat::make(trans('filament-pirsch-dashboard-widget::translations.widget.live_visitors.label'), FilamentPirsch::activeVisitors($myFilter))
                ->description(trans('filament-pirsch-dashboard-widget::translations.widget.live_visitors.description'))
                ->icon(FilamentPirschPlugin::get()->getLiveVisitorIcon())
                ->color(FilamentPirschPlugin::get()->getLiveVisitorColor()),
            Stat::make(trans('filament-pirsch-dashboard-widget::translations.widget.visitors.label'), $total_visits)
                ->description(trans('filament-pirsch-dashboard-widget::translations.widget.visitors.description', ['x' => $diffInDays]))
                ->icon(FilamentPirschPlugin::get()->getVisitorsIcon())
                ->color(FilamentPirschPlugin::get()->getVisitorsColor())
                ->chart($visit_chart),
            Stat::make(trans('filament-pirsch-dashboard-widget::translations.widget.views.label'), $total_views)
                ->description(trans('filament-pirsch-dashboard-widget::translations.widget.views.description', ['x' => $diffInDays]))
                ->icon(FilamentPirschPlugin::get()->getViewsIcon())
                ->color(FilamentPirschPlugin::get()->getViewsColor())
                ->chart($views_chart),
            Stat::make(trans('filament-pirsch-dashboard-widget::translations.widget.session.label'), (string) FilamentPirsch::sessionDuration($myFilter))
                ->description(trans('filament-pirsch-dashboard-widget::translations.widget.session.description', ['x' => $diffInDays]))
                ->icon(FilamentPirschPlugin::get()->getSessionTimeIcon())
                ->color(FilamentPirschPlugin::get()->getSessionTimeColor()),
        ];
    }
}
