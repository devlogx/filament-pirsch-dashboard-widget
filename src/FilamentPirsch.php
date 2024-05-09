<?php

namespace Devlogx\FilamentPirsch;

use Carbon\CarbonInterval;
use Devlogx\FilamentPirsch\Concerns\Filter;
use Devlogx\FilamentPirsch\Concerns\PirschClient;
use Illuminate\Support\Facades\Cache;

class FilamentPirsch
{
    protected PirschClient $client;

    /**
     * @param  float  $timeout  default 5.0
     *
     * @throws \Exception Missing pirsch client_id and client_secret
     */
    public function __construct(float $timeout = 5.0, string $baseURL = PirschClient::DEFAULT_BASE_URL)
    {
        if (! config('filament-pirsch-dashboard-widget.client_id') || ! config('filament-pirsch-dashboard-widget.client_secret')) {
            throw new \Exception('Pirsch Client ID and Client Secret are required.');
        }
        $this->client = new PirschClient($timeout, $baseURL);
    }

    /**
     * Get the Pirsch Dashboard link for the right domain.
     */
    public function getDashboardLink(): string
    {
        return 'https://' . $this->client->getDomain()->subdomain . '.pirsch.io';
    }

    private function getCachedValue(string $key, \Closure $callback): mixed
    {
        return Cache::remember($key, config('filament-pirsch-dashboard-widget.cache_time'), $callback);
    }

    /**
     * Get the active visitors, if set $rawData = true, the return value is the raw API data
     *
     * @throws \Exception
     */
    public function activeVisitors(Filter $filter, bool $rawData = false): array | string
    {
        if ($rawData) {
            return $this->client->performGet(PirschClient::ACTIVE_VISITORS_ENDPOINT, $filter);
        }

        return $this->getCachedValue('current-visitors-' . $filter->hash(), function () use ($filter) {
            return $this->client->performGet(PirschClient::ACTIVE_VISITORS_ENDPOINT, $filter)->visitors;
        });
    }

    /**
     * Get the session duration, if set $rawData = true, the return value is the raw API data
     *
     * @throws \Exception
     */
    public function sessionDuration(Filter $filter, bool $rawData = false): array | string
    {
        if ($rawData) {
            return $this->client->performGet(PirschClient::SESSION_DURATION_ENDPOINT, $filter);
        }

        return $this->getCachedValue('month-avg-time-' . $filter->hash(), function () use ($filter) {
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

    /**
     * Get the visitors, if set $rawData = true, the return value is the raw API data
     *
     * @throws \Exception
     */
    public function visitors(Filter $filter, bool $rawData = false): array | string
    {
        if ($rawData) {
            return $this->client->performGet(PirschClient::VISITORS_ENDPOINT, $filter);
        }

        return $this->getCachedValue('month-visitors-' . $filter->hash(), function () use ($filter) {
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

    /**
     * Get the page views, if set $rawData = true, the return value is the raw API data
     *
     * @throws \Exception
     */
    public function views(Filter $filter, bool $rawData = false): array | string
    {
        if ($rawData) {
            return $this->client->performGet(PirschClient::VISITORS_ENDPOINT, $filter);
        }

        return $this->getCachedValue('month-views-' . $filter->hash(), function () use ($filter) {
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

    /**
     * Get the avg. time on the page
     *
     * @throws \Exception
     */
    public function timeOnPage(Filter $filter, bool $rawData = false): array | string
    {
        if ($rawData) {
            return $this->client->performGet(PirschClient::TIME_ON_PAGE_ENDPOINT, $filter);
        }

        return $this->getCachedValue('time-on-page-' . $filter->hash(), function () use ($filter) {
            $durationDays = $this->client->performGet(PirschClient::TIME_ON_PAGE_ENDPOINT, $filter);
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

    /**
     * Get the events
     *
     * @param  bool  $rawData  Coming soon
     *
     * @throws \Exception
     */
    public function events(Filter $filter, bool $rawData = false): array | string | null
    {
        return $this->client->performGet(PirschClient::EVENTS_ENDPOINT, $filter);
    }

    /**
     * Get the event meta data
     *
     * @param  bool  $rawData  Coming soon
     *
     * @throws \Exception
     */
    public function eventMetaData(Filter $filter, bool $rawData = false): array | string
    {
        return $this->client->performGet(PirschClient::EVENT_METADATA_ENDPOINT, $filter);
    }

    /**
     * Get the languages
     *
     * @throws \Exception
     */
    public function languages(Filter $filter, bool $rawData = false): array | string
    {
        if ($rawData) {
            return $this->client->performGet(PirschClient::LANGUAGE_ENDPOINT, $filter);
        }

        return $this->getCachedValue('languages-' . $filter->hash(), function () use ($filter) {
            $languages = $this->client->performGet(PirschClient::LANGUAGE_ENDPOINT, $filter);
            $langArr = [];
            foreach ($languages as $item) {
                $language = $item->language;

                if ($language === '') {
                    $language = 'default';
                }

                $langArr[$language] = [
                    'visitors' => $item->visitors,
                    'relative_visitors' => $item->relative_visitors,
                ];
            }

            return $langArr;
        });
    }

    /**
     * Get the referrer
     *
     * @throws \Exception
     */
    public function referrer(Filter $filter, bool $rawData = false): array | string
    {
        if ($rawData) {
            return $this->client->performGet(PirschClient::REFERRER_ENDPOINT, $filter);
        }

        return $this->getCachedValue('referrer-' . $filter->hash(), function () use ($filter) {
            $referrer = $this->client->performGet(PirschClient::REFERRER_ENDPOINT, $filter);
            $refArr = [];
            foreach ($referrer as $item) {
                $name = $item->referrer_name;

                if ($name === '') {
                    $name = 'default';
                }
                $refArr[$name] = [
                    'visitors' => $item->visitors,
                    'relative_visitors' => $item->relative_visitors,
                ];
            }

            return $refArr;
        });
    }

    /**
     * Get the os used
     *
     * @throws \Exception
     */
    public function os(Filter $filter, bool $rawData = false): array | string
    {
        if ($rawData) {
            return $this->client->performGet(PirschClient::OS_ENDPOINT, $filter);
        }

        return $this->getCachedValue('os-' . $filter->hash(), function () use ($filter) {
            $os = $this->client->performGet(PirschClient::OS_ENDPOINT, $filter);
            $osArr = [];
            foreach ($os as $item) {
                $name = $item->os;

                if ($name === '') {
                    $name = 'default';
                }
                $osArr[$name] = [
                    'visitors' => $item->visitors,
                    'relative_visitors' => $item->relative_visitors,
                ];
            }

            return $osArr;
        });
    }

    /**
     * Get the platforms used
     *
     * @throws \Exception
     */
    public function platform(Filter $filter, bool $rawData = false): array | string | \stdClass
    {
        if ($rawData) {
            return $this->client->performGet(PirschClient::PLATFORM_ENDPOINT, $filter);
        }

        return $this->getCachedValue('platform-' . $filter->hash(), function () use ($filter) {
            $platform = $this->client->performGet(PirschClient::PLATFORM_ENDPOINT, $filter);

            return [
                'desktop' => [
                    'visitors' => $platform->platform_desktop,
                    'relative_visitors' => $platform->relative_platform_desktop,
                ],
                'mobile' => [
                    'visitors' => $platform->platform_mobile,
                    'relative_visitors' => $platform->relative_platform_mobile,
                ],
                'unknown' => [
                    'visitors' => $platform->platform_unknown,
                    'relative_visitors' => $platform->relative_platform_unknown,
                ],
            ];
        });
    }

    /**
     * Retrieve the keywords list
     *
     * @param  bool  $rawData  Coming soon
     *
     * @throws \Exception
     */
    public function keywords(Filter $filter, bool $rawData = false): array | string | null
    {
        return $this->client->performGet(PirschClient::KEYWORDS_ENDPOINT, $filter);
    }
}
