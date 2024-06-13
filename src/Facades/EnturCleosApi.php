<?php

namespace Ragnarok\Entur\Facades;

use Illuminate\Support\Facades\Facade;
use Ragnarok\Entur\Services\CleosAuthToken;

/**
 * @method static string getApiToken()
 * @method static string getApiUrl()
 */
class EnturCleosApi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CleosAuthToken::class;
    }
}
