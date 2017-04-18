<?php

namespace Leaps\HttpClient\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see Leaps\HttpClient
 */
class HttpClient extends Facade {

    /**
     * @return string
     */
    protected static function getFacadeAccessor() { return 'HttpClient'; }

}
