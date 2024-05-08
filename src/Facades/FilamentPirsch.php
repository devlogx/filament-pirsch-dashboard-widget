<?php

namespace Devlogx\FilamentPirsch\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Devlogx\FilamentPirsch\FilamentPirsch
 */
class FilamentPirsch extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Devlogx\FilamentPirsch\FilamentPirsch::class;
    }
}
