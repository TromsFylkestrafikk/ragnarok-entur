<?php

namespace Ragnarok\Entur\Facades;

use Illuminate\Support\Facades\Facade;
use Ragnarok\Entur\Services\EnturAuthToken;

/**
 * @method static string getApiToken()
 * @method static string getApiUrl()
 */
class EnturApi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return EnturAuthToken::class;
    }
}
