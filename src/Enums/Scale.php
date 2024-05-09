<?php

namespace Devlogx\FilamentPirsch\Enums;

enum Scale:string
{
    case SCALE_DAY = 'day';
    case SCALE_WEEK = 'week';
    case SCALE_MONTH = 'month';
    case SCALE_YEAR = 'year';
}
