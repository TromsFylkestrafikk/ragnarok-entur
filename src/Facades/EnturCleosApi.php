<?php

namespace Ragnarok\Entur\Facades;

use Illuminate\Support\Facades\Facade;
use Ragnarok\Entur\Services\CleosService;

/**
 * @method static string getApiToken()
 * @method static string getApiUrl()
 * @method static string getNextDatasetInProductUrl($reportId, $chunckId)
 * @method static string getDatasetUrl($reportId)
 * @method static string getReportCreateUrl($datasetId)
 * @method static string getReportStatusUrl($reportId)
 */
class EnturCleosApi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CleosService::class;
    }
}
