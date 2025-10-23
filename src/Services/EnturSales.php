<?php

namespace Ragnarok\Entur\Services;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Promise\Utils;
use Illuminate\Http\Response;
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

class EnturSales
{
    use LogPrintf;


    protected $sinkDisk;
    protected $cleosApi;

    public function __construct()
    {
        $disk = new SinkDisk(SinkEnturSales::$id);
        $this->sinkDisk = $disk->getDisk();
        $this->logPrintfInit("[EnTur CLEOS]: ");
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
        $client = Http::withHeaders([
            'authorization' => 'Bearer ' . EnturCleosApi::getApiToken()
        ]);

        $archive = null;

        $datasetId = $this->fetchNextDataSetId($client, $chunkId, 0);
        while ($datasetId !== null) {
            $this->debug("Processing dataset ID: %s, chunkId: %s", $datasetId, $chunkId);
            $chunkDate = Carbon::parse($chunkId)->format("Ymd");

            $datasetMetadata = $this->getDatasetMetaData($client, $datasetId);
            if (!$datasetMetadata) {
                return null;
            }

            $orderDate = Carbon::parse($datasetMetadata->orderDate)->format("Ymd");
            $orderBy = $datasetMetadata->orderBy;

            if ($orderDate === $chunkDate && $orderBy === 'CLEOS') {
                $reportId = $this->createReportFormattingJob($client, $datasetId);
                if ($reportId === null) {
                    return null;
                }

                $report = $this->pollReportStatus($client, $reportId);
                if ($report === null) {
                    return null;
                }

                $reportContent = $this->downloadReport($client, $report->signedBucketUrl);
                if ($reportContent === null) {
                    return null;
                }

                if ($archive === null) {
                    $archive = new ChunkArchive(SinkEnturSales::$id, $chunkId);
                }

                $archive->addFromString($report->reportName, $reportContent);
            } else {
                break;
            }

            $datasetId = $this->fetchNextDataSetId($client, $chunkId, $datasetId);
        }

        if ($archive !== null) {
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
        return $count;
    }

    public function destinationTables(): array
    {
        return [
            'entur_product_sales' => 'Salgsdokumentasjon pr avtale, kjøres programatisk ved daglig saldering'
        ];
    }

    protected function fetchNextDataSetId($client, $chunkId, int $datasetId): int|null
    {
        $response = $client->get(EnturCleosApi::getNextDatasetInProductUrl($datasetId, $chunkId));
        $status = $response->status();
        if ($status !== Response::HTTP_OK) {
            $this->debug("STATUS fetchNextDatasetId: %s", $status);
            return null;
        }

        // next dataset id
        return (int)$response->body();
    }

    protected function getDatasetMetaData($client, int $datasetId): object|null
    {
        $response = $client->get(EnturCleosApi::getDatasetUrl($datasetId));
        if ($response->status() !== Response::HTTP_OK) {
            $this->debug("STATUS getDatasetMetaData: %s", $response->status());
            return null;
        }

        return json_decode($response->body());
    }

    protected function createReportFormattingJob($client, int $datasetId): string|null
    {
        $response = $client->post(EnturCleosApi::getReportCreateUrl($datasetId));
        if ($response->status() !== Response::HTTP_CREATED) {
            $this->debug("STATUS createReportFormattingJob: %s", $response->status());
            return null;
        }

        $body = json_decode($response->body());
        return $body->id ?? null;
    }

    protected function pollReportStatus($client, string $reportId): object|null
    {
        $client->async();
        do {
            $promises = [$client->get(EnturCleosApi::getReportStatusUrl($reportId))];
            $responses = Utils::all($promises)->wait();
        } while ($responses[0]->status() === Response::HTTP_ACCEPTED);

        $client->async(false);

        if ($responses[0]->status() !== Response::HTTP_OK) {
            $this->debug("STATUS pollCheck %s", $responses[0]->status());
            return null;
        }

        return json_decode($responses[0]->body());
    }

    protected function downloadReport($client, string $url): string|null
    {
        $response = $client->get($url);
        if ($response->status() !== Response::HTTP_OK) {
            $this->debug("STATUS download report %s", $response->status());
            return null;
        }

        return $response->body();
    }

    protected function importFromCsv(string $path, string $chunkId)
    {
        $mapper = new CsvToTable($path, 'entur_product_sales', ['ORDERLINE_ID', 'FARE_PRODUCT_ID']);
        $mapper->prepareCsvReader(function (Reader $csv) {
            $csv->setDelimiter(';');
        });

        // use ($chunkId) - to make $chunkId available inside the preInsertRecord callback function
        $mapper->preInsertRecord(function ($csvRec, &$dbRec) use ($chunkId) {
            $dbRec['chunk_id'] = $chunkId;
        });

        $mapper->column('GL_BATCH_ID', 'group_id');
        $mapper->column('ORDERLINE_ID', 'sales_orderline_id');
        $mapper->column('FARE_PRODUCT_ID', 'sales_fare_product_id');
        $mapper->column('DISTRIBUTION_CHANNEL_REF', 'distribution_channel_ref');

        $mapper->column('ACCT_MONTH', 'accounting_month');
        $mapper->column('AGREEMENT_ORG_NO', 'organisation');
        $mapper->column('AGREEMENT_REF', 'agreement_ref');
        $mapper->column('AGREEMENT_NAME', 'agreement_description');

        $mapper->column('POS_PROVIDER_REF', 'pos_provider_ref');
        $mapper->column('POS_SUPPLIER_REF', 'pos_supplier_ref');
        $mapper->column('POS_REF', 'pos_ref');
        $mapper->column('POS_NAME', 'pos_name'); //nullable
        $mapper->column('POS_LOCATION_REF', 'pos_location_ref'); //nullable
        $mapper->column('POS_LOCATION_NAME', 'pos_location_name'); //nullable
        $mapper->column('POS_PRIVATECODE', 'pos_privatecode'); //nullable

        $mapper->column('TRANSACTION_TYPE', 'transaction_type');

        $mapper->column('ORDER_ID', 'sales_order_id');
        $mapper->column('ORDER_VERSION', 'sales_order_version');
        $mapper->column('PAYMENT_TYPE', 'sales_payment_type');
        $mapper->column('EXTERNAL_REFERENCE', 'sales_external_reference'); //nullable
        $mapper->column('SETTLEMENT_DATE', 'sales_date')->format([static::class, 'dateFormatter']);
        $mapper->column('SALES_PACKAGE_PRIVATECODE', 'sales_privatecode'); //nullable
        $mapper->column('SALES_PACKAGE_REF', 'sales_package_ref');
        $mapper->column('SALES_PACKAGE_NAME', 'sales_package_name');
        $mapper->column('DISCOUNT_RIGHT_REF', 'sales_discount_right_ref'); //nullable //UNSURE of type since always empty in csv
        $mapper->column('DISCOUNT_RIGHT_NAME', 'sales_discount_right_name'); //nullable //UNSURE of type since always empty in csv
        $mapper->column('USER_PROFILE_REF', 'sales_user_profile_ref');
        $mapper->column('USER_PROFILE_NAME', 'sales_user_profile_name');
        $mapper->column('JOURNEY_START_TIME', 'sales_start_time')->format([static::class, 'dateTimeFormatter']);
        $mapper->column('LEG_FROM_REF', 'sales_from_stop_place'); //might be null
        $mapper->column('LEG_FROM_NAME', 'sales_from_stop_place_name'); //might be null
        $mapper->column('LEG_TO_REF', 'sales_to_stop_place'); //might be null
        $mapper->column('LEG_TO_NAME', 'sales_to_stop_place_name'); // might be null
        $mapper->column('INTERVAL_ZONE_COUNT', 'sales_zone_count'); // might be null
        $mapper->column('INTERVAL_ZONES', 'sales_zones_ref'); //might be null

        $mapper->column('INTERVAL_DISTANCE', 'sales_interval_distance'); //->nullable();  //Unsure what this is all about!
        $mapper->column('LEG_SERVICEJOURNEY', 'sales_leg_servicejourney_ref'); //might be null
        $mapper->column('LEG_SERVICEJOURNEY_PCODE', 'sales_leg_servicejourney_pcode'); //might be null
        $mapper->column('LEG_LINE_PUBLICCODE', 'sales_leg_line_publiccode'); //->nullable(); //Unsure, since all values are null
        $mapper->column('ACCT_LEG_LINE_REF', 'sales_leg_line_ref'); //might be null
        $mapper->column('ACCT_LEG_LINE_NAME', 'sales_leg_line_name'); // missing in data

        $mapper->column('ANNEX_TRANSIENT_GUID', 'annex_transient_guid');
        $mapper->column('ANNEX_DESCRIPTION', 'annex_description'); //->nullable(); //missing data
        $mapper->column('ANNEX_OCCURS', 'annex_occurs'); //->nullable(); //missing data
        $mapper->column('ANNEX_AMOUNT', 'annex_amount');
        $mapper->column('ANNEX_TAX_CODE', 'annex_tax_code');
        $mapper->column('ANNEX_TAX_RATE', 'annex_tax_rate'); //V

        $mapper->column('ACCT_DATE', 'line_accounting_date')->format([static::class, 'dateFormatter']);
        $mapper->column('CLEARING_MAPPING_REF', 'line_category_ref');
        $mapper->column('CLEARING_MAPPING_NAME', 'line_category_description');
        $mapper->column('ACCT_AMOUNT', 'acct_amount'); // MIGHT DROP THIS ONE? same as annex_ammount?
        $mapper->column('CLEARING_CANCELLATION', 'line_cancellation');
        $mapper->column('ACCT_STANDARD_TAX_CODE', 'line_standard_tax_code');
        $mapper->column('ACCT_LOCAL_TAX_CODE', 'line_local_tax_code');
        $mapper->column('ACCT_LOCAL_TAX_RATE', 'line_local_tax_rate');
        $mapper->column('EST_TAX_AMOUNT', 'line_tax_amount');



        return $mapper->exec()->logSummary()->getProcessedRecords();
    }

    public function makeTargetFilename(): string
    {
        return sprintf('cleos_%s_%s.zip', config('ragnarok_entur.cleos.env'), today()->format('Y-m-d'));
    }
}
