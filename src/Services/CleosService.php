<?php

namespace Ragnarok\Entur\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class CleosService
{
    use \Ragnarok\Sink\Traits\LogPrintf;

    /**
     * API token for downloading NeTEx data from EnTur.
     *
     * @var string|null
     */
    protected $apiToken;

    /**
     * @var \Carbon\CarbonInterface|null
     */
    protected $tokenExpires;

    public function __construct()
    {
        $this->apiToken = null;
        $this->tokenExpires = null;
        $this->logPrintfInit("[EnTur CLEOS]: ");
    }

    /**
     * Request API token from EnTur.
     *
     * @return string
     */
    public function getApiToken(): string
    {
        if ($this->apiToken !== null && $this->tokenExpires->isAfter(Carbon::now())) {
            return $this->apiToken;
        }
        $this->debug('Requesting API token from EnTur.');

        $api = config('ragnarok_entur.cleos');
        $auth = array_intersect_key($api, ['client_id' => 1, 'client_secret' => 1, 'audience' => 1]);
        $auth['grant_type'] = 'client_credentials';
        $response = Http::post($api['auth_url'], $auth);
        $response->throw();
        $result = $response->json();
        $this->apiToken = $result['access_token'];

        $this->tokenExpires = Carbon::now()->addSeconds(intval($result['expires_in']));
        $this->debug("Token expires: %s ", $this->tokenExpires->format('Y-m-d H:i:s'));
        return $this->apiToken;
    }

    /**
     * Get base URL for EnTur API
     *
     * @return string
     */
    public function getApiUrl(): string
    {
        return rtrim(config('ragnarok_entur.cleos.audience'), '/');
    }

    /**
     * Get URL for fetching next Dataset in Product
     *
     * @return string
     */
    public function getNextDatasetInProductUrl($reportId, $chunckId)
    {
        //TODO: ProductId == 1056 should probably NOT be hardcoded
        $urlToUse = sprintf(
            "%s/%s/%s",
            CleosService::getApiUrl(),
            config('ragnarok_entur.cleos.api_path'),
            "partner-data/dataproduct/1056/next?idAfter={$reportId}&fromDate={$chunckId}"
        );
        return $urlToUse;
    }

    /**
     * Get URL for fetching a dataset
     *
     * @return string
     */
    public function getDatasetUrl($datasetId)
    {
        $urlToUse = sprintf(
            "%s/%s/%s",
            CleosService::getApiUrl(),
            config('ragnarok_entur.cleos.api_path'),
            "partner-data/dataset/{$datasetId}"
        );
        return $urlToUse;
    }

    /**
     * Get URL for creating and format a report
     *
     * @return string
     */
    public function getReportCreateUrl($datasetId)
    {
        $urlToUse = sprintf(
            "%s/%s/%s",
            CleosService::getApiUrl(),
            config('ragnarok_entur.cleos.api_path'),
            "partner-data/dataset/{$datasetId}/report?targetFormat=CSV"
        );
        return $urlToUse;
    }

    /**
     * Get URL for checking if report creation is done
     *
     * @return string
     */
    public function getReportStatusUrl($reportId)
    {
        $urlToUse = sprintf(
            "%s/%s/%s",
            CleosService::getApiUrl(),
            config('ragnarok_entur.cleos.api_path'),
            "partner-data/report/{$reportId}?waitFor=1"
        );
        return $urlToUse;
    }
}
