<?php

namespace Devlogx\FilamentPirsch\Concerns;

use Carbon\Carbon;

const SCALE_DAY = 'day';
const SCALE_WEEK = 'week';
const SCALE_MONTH = 'month';
const SCALE_YEAR = 'year';
const CUSTOM_METRIC_TYPE_INTEGER = 'integer';
const CUSTOM_METRIC_TYPE_FLOAT = 'float';

class Filter
{
    protected string $id = "";
    protected Carbon $from;
    protected Carbon $to;
    protected string $tz;
    protected Carbon $from_time;
    protected Carbon $to_time;
    public $start;
    public $scale;
    public $path;
    public $pattern;
    public $entry_path;
    public $exit_path;
    public $event;
    public $event_meta_key;
    // TODO $meta_...
    public $language;
    public $country;
    public $city;
    public $referrer;
    public $referrer_name;
    public $os;
    public $browser;
    public $platform;
    public $screen_class;
    public $utm_source;
    public $utm_medium;
    public $utm_campaign;
    public $utm_content;
    public $utm_term;
    // TODO $tag_...
    public $custom_metric_key;
    public $custom_metric_type;
    public $include_avg_time_on_page;
    public $offset;
    public $limit;
    public $sort;
    public $direction;
    public $search;

    public function __construct()
    {
        $this->tz = config('app.timezone');
        $this->from_time = now()->startOfDay();
        $this->to_time = now()->endOfDay();
    }

    public function toQuery():string{
        return http_build_query([
            "id" => $this->id,
            "from" => $this->from->format('Y-m-d'),
            "to" => $this->to->format('Y-m-d'),
            "tz" => $this->tz,
            "from_time" => $this->from_time->format('H:i'),
            "to_time" => $this->to_time->format('H:i'),
        ]);
    }

    public function hash():string{
        return md5($this->toQuery());
    }

    public function setFrom(Carbon $from): Filter
    {
        $this->from = $from;
        return $this;
    }

    public function setFromTime(Carbon $from_time): Filter
    {
        $this->from_time = $from_time;
        return $this;
    }

    public function setToTime(Carbon $to_time): Filter
    {
        $this->to_time = $to_time;
        return $this;
    }


    public function setTo(Carbon $to): Filter
    {
        $this->to = $to;
        return $this;
    }

    public function setId(string $id): Filter
    {
        $this->id = $id;
        return $this;
    }


}
