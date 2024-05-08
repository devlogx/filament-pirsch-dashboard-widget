<?php

namespace Devlogx\FilamentPirsch\Concerns;

use Devlogx\FilamentPirsch\Facades\FilamentPirsch;
use Devlogx\FilamentPirsch\FilamentPirschPlugin;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Support\Enums\ActionSize;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

trait HasFilter
{
    use \Filament\Pages\Dashboard\Concerns\HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(trans('filament-pirsch-dashboard-widget::translations.filter.title'))
                    ->description(trans('filament-pirsch-dashboard-widget::translations.filter.description'))
                    ->icon(fn (): string => FilamentPirschPlugin::get()->getFilterSectionIcon())
                    ->iconColor(fn (): string => FilamentPirschPlugin::get()->getFilterSectionIconColor())
                    ->headerActions([
                        Action::make('open')
                            ->label(trans('filament-pirsch-dashboard-widget::translations.filter.open_pirsch'))
                            ->hidden(fn () => ! FilamentPirschPlugin::get()->shouldShowPirschLink())
                            ->icon('heroicon-s-link')
                            ->color('primary')
                            ->size(ActionSize::Small)
                            ->url(FilamentPirsch::getDashboardLink())
                            ->openUrlInNewTab(),
                    ])
                    ->schema([
                        DateRangePicker::make('date_range')
                            ->label(trans('filament-pirsch-dashboard-widget::translations.filter.select_range'))
                            ->columnSpan('full')
                            ->displayFormat('DD.MM.YYYY')
                            ->format('d.m.Y')
                            ->startDate(now()->subDays(30), true)
                            ->endDate(now(), true)
                            ->maxDate(now()),
                    ])
                    ->columns(2),
            ]);
    }
}
