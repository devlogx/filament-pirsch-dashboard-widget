<?php

namespace Devlogx\FilamentPirsch\Concerns;

use Carbon\Carbon;
use Devlogx\FilamentPirsch\Enums\Scale;

class Filter
{
    protected string $id = '';

    protected Carbon $from;

    protected Carbon $to;

    protected string $tz;

    protected Carbon $from_time;

    protected Carbon $to_time;

    protected string $event = '';

    protected string $event_meta_key = '';

    protected Scale $scale = Scale::SCALE_DAY;

    public function __construct()
    {
        $this->tz = config('app.timezone');
        $this->from_time = now()->startOfDay();
        $this->to_time = now()->endOfDay();
    }

    /**
     * Building up the query params for the api call
     */
    public function toQuery(): string
    {
        return http_build_query([
            'id' => $this->id,
            'from' => $this->from->format('Y-m-d'),
            'to' => $this->to->format('Y-m-d'),
            'tz' => $this->tz,
            'from_time' => $this->from_time->format('H:i'),
            'to_time' => $this->to_time->format('H:i'),
            'event' => $this->event,
            'event_meta_key' => $this->event_meta_key,
            'scale' => $this->scale,
        ]);
    }

    /**
     * Generation a md5 hash out of the filter object for caching.
     */
    public function hash(): string
    {
        return md5($this->toQuery());
    }

    /**
     * The from date filter
     *
     * @return $this
     */
    public function setFrom(Carbon $from): Filter
    {
        $this->from = $from;

        return $this;
    }

    /**
     * The to date filter
     *
     * @return $this
     */
    public function setTo(Carbon $to): Filter
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Sets the start time to group results by minute in the format of Carbon. This only applies if the start and end date (to and from) are equal.
     *
     * @return $this
     */
    public function setFromTime(Carbon $from_time): Filter
    {
        $this->from_time = $from_time;

        return $this;
    }

    /**
     * Sets the end time to group results by minute in the format of Carbon. This only applies if the start and end date (to and from) are equal.
     *
     * @return $this
     */
    public function setToTime(Carbon $to_time): Filter
    {
        $this->to_time = $to_time;

        return $this;
    }

    /**
     * The domain ID. Use the list endpoint to get the domain ID for the client. (Default set by PirschClient)
     *
     * @return $this
     */
    public function setId(string $id): Filter
    {
        $this->id = $id;

        return $this;
    }

    /**
     * The name of an event to filter for.
     *
     * @return $this
     */
    public function setEvent(string $event): Filter
    {
        $this->event = $event;

        return $this;
    }

    /**
     * The event meta key to filter for. This field is used to break down a single event.
     *
     * @return $this
     */
    public function setEventMetaKey(string $event_meta_key): Filter
    {
        $this->event_meta_key = $event_meta_key;

        return $this;
    }

    /**
     * The scale to group results. Can either be day (default), week, month, or year.
     *
     * @return $this
     */
    public function setScale(Scale $scale): Filter
    {
        $this->scale = $scale;

        return $this;
    }
}
