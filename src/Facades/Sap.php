<?php

namespace Elielelie\Sap\Facades;

use Illuminate\Support\Facades\Facade;

class Sap extends Facade
{
    /**
     * Return facade accessor
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'sap';
    }
}