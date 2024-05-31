<?php

namespace Ragnarok\Entur\Services;

use League\Csv\Reader;
use Ragnarok\Sink\Traits\LogPrintf;
use Ragnarok\Sink\Services\SinkDisk;
use Ragnarok\Entur\Facades\EnturCleosApi;
use Ragnarok\Sink\Models\SinkFile;
use Ragnarok\Sink\Services\LocalFile;
use Ragnarok\Entur\Sinks\SinkEnturSales;
use Ragnarok\Sink\Services\ChunkArchive;
use Ragnarok\Sink\Services\ChunkExtractor;
use Ragnarok\Sink\Services\CsvToTable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class EnturSales {
    use LogPrintf;


    protected $sinkDisk;
    protected $cleosApi;
    protected $xEnturReportId;


    public function __construct() {
        $disk = new SinkDisk(SinkEnturSales::$id);
        $this->xEnturReportId = 0;
        $this->sinkDisk = $disk->getDisk();
        $this->cleosApi = new CleosAuthToken();
        $this->logPrintfInit("[EnTur CLEOS]: ");
    }

    public function getCleosS1Url($chunkId): string
    {
        $urlToUse = sprintf("%s/%s/%s", EnturCleosApi::getApiUrl(), config('ragnarok_entur.cleos.api_path'), "partner-reports/report/next/content?templateId=1015&idAfter={$this->xEnturReportId}&firstOrderedDate={$chunkId}");
        $this->debug("URL TO USE: %s", $urlToUse);
        return $urlToUse;
    }

    public function download($chunkId)
    {
        $token = $this->cleosApi->getApiToken();
        $this->debug("TOKEN IS: %s", $token);
        
        $archive = new ChunkArchive(SinkEnturSales::$id, $chunkId);

        $response = Http::withHeaders(['authorization' => 'Bearer ' . EnturCleosApi::getApiToken()])
            ->get($this->getCleosS1Url($chunkId));

        $this->debug("status: %d", $response->status());
        $status = $response->status();
        if($status == 200) {
            $archive->addFromString("CLEOS-S1-{$chunkId}.csv", $response->body());
            $nextReport = intval($response->header("x-entur-report-id"));
            $this->xEnturReportId = $nextReport;
        }

        $archive->save();
        return $archive->getFile();
    }

    public function import(string $chunkId, SinkFile $file)
    {
        $extractor = new ChunkExtractor(SinkEnturSales::$id, $file);
        $count = 0;
        foreach ($extractor->getFiles() as $index => $path) {
            $this->debug("importing file: %s", $path);
            $count += $this->importFromCsv($path, $chunkId);
        }
        $this->cleanup();
        return $count;
    }

    public function destinationTables(): array
    {
        return [
            'entur_product_sales'
        ];
    }

    protected function importFromCsv(string $path, string $chunkId)
    {
        $mapper = new CsvToTable($path, $this->destinationTables()[0], ['SALES_ORDERLINE_ID', 'SALES_FARE_PRODUCT_ID']);
        $mapper->prepareCsvReader(function (Reader $csv) {
            $csv->setDelimiter(';');
        });

        $mapper->column('GROUP_ID', 'group_id');
        $mapper->column('SALES_ORDERLINE_ID', 'sales_orderline_id');
        $mapper->column('SALES_FARE_PRODUCT_ID','sales_fare_product_id');
        $mapper->column('DISTRIBUTION_CHANNEL_REF', 'distribution_channel_ref');

        // use ($chunkId) - to make $chunkId available inside the preInsertRecord callback function
        $mapper->preInsertRecord(function ($csvRec, &$dbRec) use ($chunkId) {
            $dbRec['chunk_id'] = $chunkId;
        });

        return $mapper->
            exec()->
            logSummary()->
            getProcessedRecords();
    }

    public function makeTargetFilename(): string
    {
        return sprintf('cleos_%s_%s.zip', config('ragnarok_entur.cleos.env'), today()->format('Y-m-d'));
    }
}

// GROUP_ID
// ACCOUNTING_MONTH
// ORGANISATION
// AGREEMENT_REF
// AGREEMENT_DESCRIPTION
// DISTRIBUTION_CHANNEL_REF
// POS_PROVIDER_REF
// POS_SUPPLIER_REF
// POS_REF
// POS_NAME
// POS_LOCATION_REF
// POS_LOCATION_NAME
// POS_PRIVATECODE
// TRANSACTION_TYPE
// SALES_ORDER_ID
// SALES_ORDER_VERSION
// SALES_PAYMENT_TYPE
// SALES_EXTERNAL_REFERENCE
// SALES_ORDERLINE_ID
// SALES_FARE_PRODUCT_ID
// SALES_PRIVATECODE
// SALES_DATE
// SALES_PACKAGE_REF
// SALES_PACKAGE_NAME
// SALES_DISCOUNT_RIGHT_REF
// SALES_DISCOUNT_RIGHT_NAME
// SALES_USER_PROFILE_REF
// SALES_USER_PROFILE_NAME
// SALES_START_TIME
// SALES_FROM_STOP_PLACE
// SALES_FROM_STOP_PLACE_NAME
// SALES_TO_STOP_PLACE
// SALES_TO_STOP_PLACE_NAME
// SALES_ZONE_COUNT
// SALES_ZONES_REF
// SALES_INTERVAL_DISTANCE
// SALES_LEG_SERVICEJOURNEY_REF
// SALES_LEG_SERVICEJOURNEY_PCODE
// SALES_LEG_LINE_PUBLICCODE
// SALES_LEG_LINE_REF
// SALES_LEG_LINE_NAME
// ANNEX_TRANSIENT_GUID
// ANNEX_DESCRIPTION
// ANNEX_OCCURS
// ANNEX_AMOUNT
// ANNEX_TAX_CODE
// ANNEX_TAX_RATE
// LINE_ID
// LINE_ACCOUNTING_DATE
// LINE_CATEGORY_REF
// LINE_CATEGORY_DESCRIPTION
// LINE_AMOUNT
// LINE_CANCELLATION
// LINE_STANDARD_TAX_CODE
// LINE_LOCAL_TAX_CODE
// LINE_LOCAL_TAX_RATE
// LINE_TAX_AMOUNT