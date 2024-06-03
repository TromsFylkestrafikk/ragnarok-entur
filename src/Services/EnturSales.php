<?php

namespace Ragnarok\Entur\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

use League\Csv\Reader;
use Ragnarok\Sink\Traits\LogPrintf;
use Ragnarok\Sink\Services\SinkDisk;
use Ragnarok\Entur\Facades\EnturCleosApi;
use Ragnarok\Entur\Sinks\SinkEnturSales;
use Ragnarok\Sink\Services\ChunkArchive;
use Ragnarok\Sink\Services\ChunkExtractor;
use Ragnarok\Sink\Services\CsvToTable;
use Ragnarok\Sink\Models\SinkFile;

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
        //$urlToUse = sprintf("%s/%s/%s", EnturCleosApi::getApiUrl(), config('ragnarok_entur.cleos.api_path'), "partner-reports/report/next/content?templateId=1015&idAfter={$this->xEnturReportId}&firstOrderedDate=2024-05-31");
        $this->debug("URL TO USE: %s", $urlToUse);
        return $urlToUse;
    }

    public static function dateFormatter($date)
    {
        return (new Carbon($date))->format('Y-m-d');
    }

    public static function dateTimeFormatter($date)
    {
        return (new Carbon($date))->format('Y-m-d H:i:s');
    }

    public function download($chunkId): SinkFile|null
    {
        $token = $this->cleosApi->getApiToken();
        $this->debug("TOKEN IS: %s", $token);
        
        

        $response = Http::withHeaders(['authorization' => 'Bearer ' . EnturCleosApi::getApiToken()])
            ->get($this->getCleosS1Url($chunkId));

   
        $this->debug("status: %d", $response->status());
        $status = $response->status();
        if($status == 200) {
            $archive = new ChunkArchive(SinkEnturSales::$id, $chunkId);
            $archive->addFromString("CLEOS-S1-{$chunkId}.csv", $response->body());
            $nextReport = intval($response->header("x-entur-report-id"));
            $this->xEnturReportId = $nextReport;
            $archive->save();
            return $archive->getFile();
        }
        return null;
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

    protected function importFromCsv(string $path, string $chunkId) {
        $mapper = new CsvToTable($path, $this->destinationTables()[0], ['SALES_ORDERLINE_ID', 'SALES_FARE_PRODUCT_ID']);
        $mapper->prepareCsvReader(function (Reader $csv) {
            $csv->setDelimiter(';');
        });

        // use ($chunkId) - to make $chunkId available inside the preInsertRecord callback function
        $mapper->preInsertRecord(function ($csvRec, &$dbRec) use ($chunkId) {
            $dbRec['chunk_id'] = $chunkId;
        });

        $mapper->column('GROUP_ID', 'group_id');
        $mapper->column('SALES_ORDERLINE_ID', 'sales_orderline_id');
        $mapper->column('SALES_FARE_PRODUCT_ID','sales_fare_product_id');
        $mapper->column('DISTRIBUTION_CHANNEL_REF', 'distribution_channel_ref');

        $mapper->column('ACCOUNTING_MONTH', 'accounting_month');
        $mapper->column('ORGANISATION', 'organisation');
        $mapper->column('AGREEMENT_REF', 'agreement_ref');
        $mapper->column('AGREEMENT_DESCRIPTION', 'agreement_description');

        $mapper->column('POS_PROVIDER_REF', 'pos_provider_ref');
        $mapper->column('POS_SUPPLIER_REF', 'pos_supplier_ref');
        $mapper->column('POS_REF', 'pos_ref');
        $mapper->column('POS_NAME', 'pos_name'); //nullable
        $mapper->column('POS_LOCATION_REF', 'pos_location_ref'); //nullable
        $mapper->column('POS_LOCATION_NAME', 'pos_location_name'); //nullable
        $mapper->column('POS_PRIVATECODE', 'pos_privatecode'); //nullable
        
        $mapper->column('TRANSACTION_TYPE', 'transaction_type');

        $mapper->column('SALES_ORDER_ID', 'sales_order_id');
        $mapper->column('SALES_ORDER_VERSION', 'sales_order_version');
        $mapper->column('SALES_PAYMENT_TYPE', 'sales_payment_type');
        $mapper->column('SALES_EXTERNAL_REFERENCE', 'sales_external_reference'); //nullable
        $mapper->column('SALES_DATE', 'sales_date')->format([static::class, 'dateFormatter']);
        $mapper->column('SALES_PRIVATECODE', 'sales_privatecode'); //nullable
        $mapper->column('SALES_PACKAGE_REF', 'sales_package_ref'); 
        $mapper->column('SALES_PACKAGE_NAME', 'sales_package_name');
        $mapper->column('SALES_DISCOUNT_RIGHT_REF', 'sales_discount_right_ref'); //nullable //UNSURE of type since always empty in csv
        $mapper->column('SALES_DISCOUNT_RIGHT_NAME', 'sales_discount_right_name'); //nullable //UNSURE of type since always empty in csv
        $mapper->column('SALES_USER_PROFILE_REF', 'sales_user_profile_ref');
        $mapper->column('SALES_USER_PROFILE_NAME', 'sales_user_profile_name');
        $mapper->column('SALES_START_TIME', 'sales_start_time')->format([static::class, 'dateTimeFormatter']);
        $mapper->column('SALES_FROM_STOP_PLACE', 'sales_from_stop_place'); //might be null
        $mapper->column('SALES_FROM_STOP_PLACE_NAME', 'sales_from_stop_place_name'); //might be null
        $mapper->column('SALES_TO_STOP_PLACE', 'sales_to_stop_place'); //might be null
        $mapper->column('SALES_TO_STOP_PLACE_NAME', 'sales_to_stop_place_name'); // might be null
        $mapper->column('SALES_ZONE_COUNT', 'sales_zone_count'); // might be null
        $mapper->column('SALES_ZONES_REF', 'sales_zones_ref'); //might be null

        $mapper->column('SALES_INTERVAL_DISTANCE', 'sales_interval_distance'); //->nullable();  //Unsure what this is all about!
        $mapper->column('SALES_LEG_SERVICEJOURNEY_REF', 'sales_leg_servicejourney_ref'); //might be null
        $mapper->column('SALES_LEG_SERVICEJOURNEY_PCODE', 'sales_leg_servicejourney_pcode'); //might be null
        $mapper->column('SALES_LEG_LINE_PUBLICCODE', 'sales_leg_line_publiccode'); //->nullable(); //Unsure, since all values are null
        $mapper->column('SALES_LEG_LINE_REF', 'sales_leg_line_ref'); //might be null
        $mapper->column('SALES_LEG_LINE_NAME', 'sales_leg_line_name'); // missing in data

        $mapper->column('ANNEX_TRANSIENT_GUID', 'annex_transient_guid');
        $mapper->column('ANNEX_DESCRIPTION', 'annex_description'); //->nullable(); //missing data
        $mapper->column('ANNEX_OCCURS', 'annex_occurs'); //->nullable(); //missing data
        $mapper->column('ANNEX_AMOUNT', 'annex_amount');
        $mapper->column('ANNEX_TAX_CODE', 'annex_tax_code');
        $mapper->column('ANNEX_TAX_RATE', 'annex_tax_rate');

        $mapper->column('LINE_ID','line_id');
        $mapper->column('LINE_ACCOUNTING_DATE', 'line_accounting_date')->format([static::class, 'dateFormatter']);;
        $mapper->column('LINE_CATEGORY_REF', 'line_category_ref');
        $mapper->column('LINE_CATEGORY_DESCRIPTION', 'line_category_description');
        $mapper->column('LINE_AMOUNT', 'line_amount');
        $mapper->column('LINE_CANCELLATION', 'line_cancellation');
        $mapper->column('LINE_STANDARD_TAX_CODE', 'line_standard_tax_code');
        $mapper->column('LINE_LOCAL_TAX_CODE', 'line_local_tax_code');
        $mapper->column('LINE_LOCAL_TAX_RATE', 'line_local_tax_rate');
        $mapper->column('LINE_TAX_AMOUNT', 'line_tax_amount');



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
