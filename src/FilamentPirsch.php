<?php

namespace Devlogx\FilamentPirsch;

use Carbon\CarbonInterval;
use Devlogx\FilamentPirsch\Concerns\Filter;
use Devlogx\FilamentPirsch\Concerns\PirschClient;
use Illuminate\Support\Facades\Cache;

class FilamentPirsch
{
    protected PirschClient $client;

    public function __construct($timeout = 5.0, $baseURL = PirschClient::DEFAULT_BASE_URL)
    {
        if (! config('filament-pirsch-dashboard-widget.client_id') || ! config('filament-pirsch-dashboard-widget.client_secret')) {
            throw new \Exception('Pirsch Client ID and Client Secret are required.');
        }
        $this->client = new PirschClient($timeout, $baseURL);
    }

    public function getDashboardLink(): string
    {
        return 'https://' . $this->client->getDomain()->subdomain . '.pirsch.io';
    }

    public function activeVisitors(Filter $filter)
    {
        $key = 'current-visitors-' . $filter->hash();

        return Cache::remember($key, config("filament-pirsch-dashboard-widget.cache_time"), function () use ($filter) {
            return $this->client->performGet(PirschClient::ACTIVE_VISITORS_ENDPOINT, $filter)->visitors;
        });

    }

    public function sessionDuration(Filter $filter)
    {
        $key = 'month-avg-time-' . $filter->hash();

        return Cache::remember($key, config("filament-pirsch-dashboard-widget.cache_time"), function () use ($filter) {
            $durationDays = $this->client->performGet(PirschClient::SESSION_DURATION_ENDPOINT, $filter);
            $avgTimeArray = array_map(function ($item) {
                return (int) $item->average_time_spent_seconds;
            }, $durationDays);
            if (count($avgTimeArray) == 2) {
                $avgDuration = array_sum($avgTimeArray) / 2;
            } else {
                $avgDuration = array_sum($avgTimeArray);
            }

            return CarbonInterval::seconds($avgDuration)->cascade()->format('%I:%S');
        });
    }

    public function visitors(Filter $filter): array
    {
        $key = 'month-visitors-' . $filter->hash();

        return Cache::remember($key, config("filament-pirsch-dashboard-widget.cache_time"), function () use ($filter) {
            $stats = $this->client->performGet(PirschClient::VISITORS_ENDPOINT, $filter);
            $visitsArray = array_map(function ($item) {
                return (int) $item->visitors;
            }, $stats);
            $totalVisits = array_sum($visitsArray);

            return [
                $totalVisits,
                $visitsArray,
            ];
        });

    }

    public function views(Filter $filter)
    {
        $key = 'month-views-' . $filter->hash();

        return Cache::remember($key, config("filament-pirsch-dashboard-widget.cache_time"), function () use ($filter) {
            $stats = $this->client->performGet(PirschClient::VISITORS_ENDPOINT, $filter);
            $pageViewsArray = array_map(function ($item) {
                return (int) $item->views;
            }, $stats);
            $totalPageViews = array_sum($pageViewsArray);

            return [
                $totalPageViews,
                $pageViewsArray,
            ];
        });

    }
}
