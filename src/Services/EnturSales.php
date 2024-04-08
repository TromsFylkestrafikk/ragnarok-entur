<?php

namespace Ragnarok\Entur\Services;

use League\Csv\Reader;
use Ragnarok\Sink\Traits\LogPrintf;
use Ragnarok\Sink\Services\SinkDisk;
use Ragnarok\Sink\Models\SinkFile;
use Ragnarok\Entur\Sinks\SinkEnturSales;
use Ragnarok\Sink\Services\ChunkArchive;
use Ragnarok\Sink\Services\ChunkExtractor;
use Ragnarok\Sink\Services\CsvToTable;

class EnturSales {
    use LogPrintf;


    protected $sinkDisk;


    public function __construct() {
        $disk = new SinkDisk(SinkEnturSales::$id);
        $this->sinkDisk = $disk->getDisk();
        $this->logPrintfInit("[EnTur CLEOS]: ");
    }

    public function download($chunkId)
    {
        $this->debug("ChunkID: %s", $chunkId);
        $archive = new ChunkArchive(SinkEnturSales::$id, $chunkId);
        $archive->addFromString("tfk-cleos.csv", $this->sinkDisk->get("tfk-cleos.csv"));
        $archive->save();
        return $archive->getFile();
        //$this->debug("%s", $this->sinkDisk->get("tfk-cleos.csv"));
        //return $this->sinkDisk->get("tfk-cleos.csv");
    }

    public function import(string $chunkId, SinkFile $file)
    {
        $extractor = new ChunkExtractor(SinkEnturSales::$id, $file);
        $count = 0;
        foreach ($extractor->getFiles() as $index => $path) {
            $this->debug("importing file: %s", $path);
            $count += $this->importFromCsv($path);
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

    protected function importFromCsv(string $path)
    {
        $mapper = new CsvToTable($path, $this->destinationTables()[0], ['SALES_ORDERLINE_ID', 'SALES_FARE_PRODUCT_ID']);
        $mapper->prepareCsvReader(function (Reader $csv) {
            $csv->setDelimiter(';');
        });

        $mapper->column('SALES_ORDERLINE_ID', 'sales_orderline_id');
        $mapper->column('SALES_FARE_PRODUCT_ID','sales_fare_product_id');
        $mapper->column('DISTRIBUTION_CHANNEL_REF', 'distribution_channel_ref');

        return $mapper->
            exec()->
            logSummary()->
            getProcessedRecords();
    }
}

// $table->uuid('sales_orderline_id');
// $table->uuid('sales_fare_product_id');
// $table->string('distribution_channel_ref');
// $table->string('sales_order_id');
// $table->integer('sales_order_version');
// $table->string('sales_payment_type');
// $table->date('sales_date');

// $table->string('sales_package_ref');
// $table->string('sales_package_name');

// $table->string('sales_user_profile_ref');
// $table->string('sales_user_profile_name');

// $table->dateTime('sales_start_time');

// $table->string('sales_from_stop_place');
// $table->string('sales_from_stop_name');
// $table->string('sales_top_stop_place');
// $table->string('sales_top_stop_place_name');

// $table->integer('sales_zone_count');
// $table->string('sales_zones_ref');

// $table->float('annex_amount');