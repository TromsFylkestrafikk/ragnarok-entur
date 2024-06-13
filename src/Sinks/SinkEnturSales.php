<?php

namespace Ragnarok\Entur\Sinks;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Ragnarok\Entur\Services\EnturSales;
use Ragnarok\Sink\Models\SinkFile;
use Ragnarok\Sink\Sinks\SinkBase;

class SinkEnturSales extends SinkBase
{
    public static $id = "entur-sales";
    public static $title = "Entur Salesdata";
    public $cron = '30 04 * * *';

    protected $service;

    public $singleState = false;

    public function __construct()
    {
        $this->service = new EnturSales();
    }

    public function import(string $chunkId, SinkFile $file): int
    {
        return $this->service->import($chunkId, $file);
    }

    /**
     * @inheritdoc
     */
    public function fetch(string $id): SinkFile|null
    {
        return $this->service->download($id);
    }

    /**
     * @inheritdoc
     */
    public function deleteImport(string $id, SinkFile $file): bool
    {
        $tables = $this->destinationTables();

        $tableKeys = array_keys($tables);
        foreach ($tableKeys as $table) {
            DB::table($table)->where('chunk_id', $id)->delete();
        }

        return true;
    }

    public function destinationTables(): array
    {
        return $this->service->destinationTables();
    }

    /**
     * @inheritdoc
     */
    public function getFromDate(): Carbon
    {
        // Lets use already provided/stored data as initial date.
        return Carbon::parse("2024-03-07");
    }

    /**
     * @inheritdoc
     */
    public function getToDate(): Carbon
    {
        return Carbon::today();
    }
}
